<?php

namespace Tests\Feature;

use App\Mail\AppointmentRescheduledPatientMail;
use App\Mail\AppointmentStatusChangedPatientMail;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\BranchClosure;
use App\Models\BranchSchedule;
use App\Models\Service;
use App\Models\User;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminAppointmentActionsTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;

    protected Service $service;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Cabang Solo',
            'slug' => 'cabang-solo',
            'address' => 'Jl. Veteran No. 12',
            'phone_wa' => '081234567890',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name' => 'Konsultasi Ortotik',
            'slug' => 'konsultasi-ortotik',
            'is_active' => true,
        ]);

        $this->branch->services()->attach($this->service->id);

        // Buat jadwal untuk semua hari kerja (1-6) jam 09:00 - 12:00, durasi 60m, kapasitas 1
        for ($day = 1; $day <= 6; $day++) {
            BranchSchedule::create([
                'branch_id' => $this->branch->id,
                'day_of_week' => $day,
                'is_open' => true,
                'open_time' => '09:00:00',
                'close_time' => '12:00:00',
                'slot_duration_minutes' => 60,
                'capacity_per_slot' => 1,
            ]);
        }

        $this->admin = User::factory()->create([
            'email' => 'admin@sam.test',
        ]);
    }

    public function test_admin_can_reschedule_appointment_and_mail_is_queued(): void
    {
        Mail::fake();

        // Target hari kerja di masa depan (bukan hari Minggu)
        $date1 = Carbon::today('Asia/Jakarta')->addDays(3);
        if ($date1->dayOfWeekIso === 7) {
            $date1->addDay();
        }

        $date2 = $date1->copy()->addDays(2);
        if ($date2->dayOfWeekIso === 7) {
            $date2->addDay();
        }

        $appointment = Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $date1->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'status' => 'confirmed',
            'patient_name' => 'Budi Santoso',
            'patient_email' => 'pasien@test.com',
            'patient_phone' => '6281234567891',
            'patient_age' => 30,
            'complaint' => 'Pemeriksaan rutin',
        ]);

        $slotService = app(SlotService::class);
        $newTargetDate = $date2->format('Y-m-d');
        $newStartTime = '10:00';

        $actionData = [
            'appointment_date' => $newTargetDate,
            'start_time' => $newStartTime,
        ];

        // Jalankan logika transaksi yang sama persis dengan AppointmentResource::rescheduleAction()
        $result = DB::transaction(function () use ($appointment, $actionData, $slotService) {
            $locked = Appointment::where('id', $appointment->id)->lockForUpdate()->first();
            $branch = Branch::where('id', $locked->branch_id)->lockForUpdate()->first();

            $targetDate = Carbon::parse($actionData['appointment_date'], 'Asia/Jakarta')->startOfDay();
            if (! $slotService->isDateBookable($branch, $targetDate)) {
                return false;
            }

            $startTime = substr((string) $actionData['start_time'], 0, 5);
            $slots = collect($slotService->slotsFor($branch, $targetDate));
            $matchingSlot = $slots->firstWhere('start', $startTime);

            if (! $matchingSlot || ! $matchingSlot['available']) {
                return false;
            }

            $oldDateFormatted = $locked->appointment_date ? $locked->appointment_date->format('d/m/Y') : '-';
            $oldTimeFormatted = substr((string) $locked->start_time, 0, 5);

            $timestamp = now('Asia/Jakarta')->format('d/m/Y H:i');
            $logText = "[{$timestamp}] Jadwal diubah oleh Admin dari {$oldDateFormatted} {$oldTimeFormatted} ke {$targetDate->format('d/m/Y')} {$startTime} WIB.";
            $updatedNotes = trim($locked->admin_notes ? $locked->admin_notes."\n".$logText : $logText);

            $locked->update([
                'appointment_date' => $targetDate->format('Y-m-d'),
                'start_time' => "{$startTime}:00",
                'end_time' => '11:00:00',
                'admin_notes' => $updatedNotes,
            ]);

            return true;
        });

        $this->assertTrue($result);

        $appointment->refresh();
        $this->assertEquals($newTargetDate, $appointment->appointment_date->format('Y-m-d'));
        $this->assertEquals('10:00:00', $appointment->start_time);
        $this->assertStringContainsString('Jadwal diubah oleh Admin', $appointment->admin_notes);

        Mail::assertQueued(AppointmentRescheduledPatientMail::class, function ($mail) use ($appointment) {
            return $mail->appointment->id === $appointment->id;
        });
    }

    public function test_cannot_reschedule_to_full_slot(): void
    {
        $targetDate = Carbon::today('Asia/Jakarta')->addDays(3);
        if ($targetDate->dayOfWeekIso === 7) {
            $targetDate->addDay();
        }

        // Buat janji lain di jam 09:00 (slot capacity = 1)
        Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'status' => 'confirmed',
            'patient_name' => 'Pasien Pertama',
            'patient_email' => 'pasien1@test.com',
            'patient_phone' => '6281234567891',
            'patient_age' => 28,
            'complaint' => 'Pemeriksaan rutin',
        ]);

        $slotService = app(SlotService::class);
        $slots = $slotService->slotsFor($this->branch, $targetDate);
        $slot09 = collect($slots)->firstWhere('start', '09:00');

        $this->assertFalse($slot09['available'], 'Slot 09:00 harusnya penuh (available = false).');
    }

    public function test_cannot_reschedule_to_closed_date(): void
    {
        $targetDate = Carbon::today('Asia/Jakarta')->addDays(4);
        if ($targetDate->dayOfWeekIso === 7) {
            $targetDate->addDay();
        }

        // Pasang closure libur
        BranchClosure::create([
            'branch_id' => $this->branch->id,
            'date' => $targetDate->format('Y-m-d'),
            'reason' => 'Libur Nasional',
            'is_closed' => true,
        ]);

        $slotService = app(SlotService::class);
        $this->assertFalse($slotService->isDateBookable($this->branch, $targetDate));
    }

    public function test_admin_cancellation_frees_slot_and_queues_email(): void
    {
        Mail::fake();

        $targetDate = Carbon::today('Asia/Jakarta')->addDays(3);
        if ($targetDate->dayOfWeekIso === 7) {
            $targetDate->addDay();
        }

        $appointment = Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'status' => 'confirmed',
            'patient_name' => 'Siti Nurhaliza',
            'patient_email' => 'pasien@test.com',
            'patient_phone' => '6281234567892',
            'patient_age' => 35,
            'complaint' => 'Konsultasi ortotik',
        ]);

        $slotService = app(SlotService::class);

        // Sebelum batal: slot 09:00 penuh
        $slotsBefore = $slotService->slotsFor($this->branch, $targetDate);
        $slot09Before = collect($slotsBefore)->firstWhere('start', '09:00');
        $this->assertFalse($slot09Before['available']);

        // Simulasikan aksi pembatalan oleh admin
        $reason = 'Pasien meminta reschedule ke bulan depan';
        $timestamp = now('Asia/Jakarta')->format('d/m/Y H:i');
        $logText = "[{$timestamp}] Dibatalkan oleh Admin: {$reason}.";
        $updatedNotes = trim($appointment->admin_notes ? $appointment->admin_notes."\n".$logText : $logText);

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_by' => 'admin',
            'admin_notes' => $updatedNotes,
        ]);

        $appointment->refresh();
        $this->assertEquals('cancelled', $appointment->status);
        $this->assertEquals('admin', $appointment->cancelled_by);
        $this->assertStringContainsString('Dibatalkan oleh Admin', $appointment->admin_notes);

        // Setelah batal: slot 09:00 otomatis kosong & tersedia
        $slotsAfter = $slotService->slotsFor($this->branch, $targetDate);
        $slot09After = collect($slotsAfter)->firstWhere('start', '09:00');
        $this->assertTrue($slot09After['available'], 'Slot harusnya otomatis kosong kembali.');

        Mail::assertQueued(AppointmentStatusChangedPatientMail::class, function ($mail) use ($appointment) {
            return $mail->appointment->id === $appointment->id;
        });
    }
}
