<?php

namespace App\Http\Controllers;

use App\Http\Requests\RescheduleAppointmentRequest;
use App\Models\Appointment;
use App\Models\SiteSetting;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ManageAppointmentController extends Controller
{
    /**
     * Menampilkan halaman kelola janji temu publik (/janji/{code}).
     */
    public function show(string $code): View
    {
        $appointment = Appointment::where('code', $code)
            ->with(['branch', 'service'])
            ->firstOrFail();

        $settings = SiteSetting::allCached();

        $minHours = (int) SiteSetting::get('general.appointment_reschedule_min_hours', 24);

        $appointmentDateTime = Carbon::parse(
            $appointment->appointment_date->format('Y-m-d').' '.$appointment->start_time,
            'Asia/Jakarta'
        );

        $hoursUntilAppointment = Carbon::now('Asia/Jakarta')->diffInHours($appointmentDateTime, false);

        $isActiveStatus = in_array($appointment->status, ['pending', 'confirmed']);
        $canReschedule = $isActiveStatus && ($hoursUntilAppointment >= $minHours);
        $canCancel = $isActiveStatus;

        return view('pages.appointment-manage', compact(
            'appointment',
            'settings',
            'canReschedule',
            'canCancel',
            'minHours',
            'hoursUntilAppointment'
        ));
    }

    /**
     * Memproses permintaan perubahan jadwal (reschedule).
     */
    public function reschedule(
        string $code,
        RescheduleAppointmentRequest $request,
        SlotService $slotService
    ): RedirectResponse {
        $appointment = Appointment::where('code', $code)
            ->with('branch')
            ->firstOrFail();

        $minHours = (int) SiteSetting::get('general.appointment_reschedule_min_hours', 24);

        $appointmentDateTime = Carbon::parse(
            $appointment->appointment_date->format('Y-m-d').' '.$appointment->start_time,
            'Asia/Jakarta'
        );

        $hoursUntilAppointment = Carbon::now('Asia/Jakarta')->diffInHours($appointmentDateTime, false);

        if (! in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->withErrors([
                'reschedule' => 'Jadwal tidak dapat diubah karena status janji temu saat ini adalah '.$appointment->status.'.',
            ]);
        }

        if ($hoursUntilAppointment < $minHours) {
            return back()->withErrors([
                'reschedule' => "Perubahan jadwal hanya dapat dilakukan maksimal {$minHours} jam sebelum waktu janji temu.",
            ]);
        }

        $validated = $request->validated();
        $targetDate = Carbon::parse($validated['appointment_date'], 'Asia/Jakarta')->startOfDay();
        $startTime = substr((string) $validated['start_time'], 0, 5);

        try {
            DB::transaction(function () use ($appointment, $targetDate, $startTime, $slotService) {
                if (! $slotService->isDateBookable($appointment->branch, $targetDate)) {
                    throw ValidationException::withMessages([
                        'appointment_date' => 'Tanggal baru tidak tersedia untuk reservasi.',
                    ]);
                }

                $availableSlots = collect($slotService->slotsFor($appointment->branch, $targetDate));
                $matchingSlot = $availableSlots->firstWhere('start', $startTime);

                if (! $matchingSlot || ! $matchingSlot['available']) {
                    throw ValidationException::withMessages([
                        'start_time' => 'Slot waktu baru sudah penuh atau tidak tersedia.',
                    ]);
                }

                // Cek kapasitas slot dengan lock
                $activeAppointmentsCount = Appointment::where('branch_id', $appointment->branch_id)
                    ->where('id', '!=', $appointment->id)
                    ->whereDate('appointment_date', $targetDate->format('Y-m-d'))
                    ->where(function ($q) use ($startTime) {
                        $q->where('start_time', $startTime)
                            ->orWhere('start_time', "{$startTime}:00");
                    })
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->lockForUpdate()
                    ->count();

                if ($activeAppointmentsCount >= $matchingSlot['capacity']) {
                    throw ValidationException::withMessages([
                        'start_time' => 'Slot sudah terisi, silakan pilih jam lain.',
                    ]);
                }

                $endTime = $matchingSlot['end'];

                $appointment->update([
                    'appointment_date' => $targetDate->format('Y-m-d'),
                    'start_time' => "{$startTime}:00",
                    'end_time' => strlen($endTime) === 5 ? "{$endTime}:00" : $endTime,
                ]);
            });

            return redirect()->route('appointment.manage', $appointment->code)
                ->with('status', 'Jadwal kunjungan Anda berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Membatalkan janji temu oleh pasien.
     */
    public function cancel(string $code): RedirectResponse
    {
        $appointment = Appointment::where('code', $code)->firstOrFail();

        if (! in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->withErrors([
                'cancel' => 'Janji temu tidak dapat dibatalkan karena sudah berstatus '.$appointment->status.'.',
            ]);
        }

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_by' => 'patient',
        ]);

        return redirect()->route('appointment.manage', $appointment->code)
            ->with('status', 'Janji temu Anda telah berhasil dibatalkan.');
    }
}
