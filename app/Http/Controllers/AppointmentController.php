<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\SiteSetting;
use App\Services\SlotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Menampilkan form pendaftaran janji temu pasien.
     */
    public function index(): View
    {
        $settings = SiteSetting::allCached();

        $branches = Branch::active()
            ->with(['services' => fn ($q) => $q->where('is_active', true)])
            ->get();

        return view('pages.appointment', compact('branches', 'settings'));
    }

    /**
     * Memproses pendaftaran janji temu baru.
     */
    public function store(StoreAppointmentRequest $request, SlotService $slotService): RedirectResponse
    {
        $validated = $request->validated();

        $prescriptionPath = null;
        if ($request->hasFile('prescription') && $request->file('prescription')->isValid()) {
            // Simpan ke disk 'local' (storage/app/private/prescriptions) dengan nama file terenkripsi
            $prescriptionPath = $request->file('prescription')->store('prescriptions', 'local');
        }

        $bookData = [
            'branch_id' => (int) $validated['branch_id'],
            'service_id' => (int) $validated['service_id'],
            'appointment_date' => $validated['appointment_date'],
            'start_time' => $validated['start_time'],
            'patient_name' => $validated['patient_name'],
            'patient_email' => $validated['patient_email'],
            'patient_phone' => $validated['patient_phone'],
            'patient_age' => $validated['patient_age'],
            'complaint' => $validated['complaint'],
            'prescription_path' => $prescriptionPath,
        ];

        $appointment = $slotService->book($bookData);

        return redirect()->route('appointment.confirmation', ['code' => $appointment->code])
            ->with('status', 'Janji temu Anda berhasil dibuat! Simpan kode janji untuk melacak status kunjungan.');
    }

    /**
     * Menampilkan halaman konfirmasi sukses pendaftaran janji temu.
     */
    public function confirmation(string $code): View
    {
        $appointment = Appointment::where('code', $code)
            ->with(['branch', 'service'])
            ->firstOrFail();

        $settings = SiteSetting::allCached();

        return view('pages.appointment-confirmation', compact('appointment', 'settings'));
    }
}
