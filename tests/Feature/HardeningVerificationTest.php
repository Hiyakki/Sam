<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\BranchSchedule;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class HardeningVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;

    protected Service $service;

    protected SlotService $slotService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->slotService = app(SlotService::class);

        SiteSetting::set('contact.whatsapp', '081234567890');
        SiteSetting::set('general.appointment_max_days', '60');
        SiteSetting::set('general.slot_buffer_minutes', '60');

        $this->branch = Branch::create([
            'name' => 'SAM Cabang Solo',
            'slug' => 'sam-solo',
            'address' => 'Jl. Veteran No. 12, Surakarta',
            'phone_wa' => '081234567890',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name' => 'Konsultasi Ortotik Kustom',
            'slug' => 'konsultasi-ortotik',
            'is_active' => true,
        ]);

        $this->branch->services()->attach($this->service->id);

        for ($day = 1; $day <= 7; $day++) {
            BranchSchedule::create([
                'branch_id' => $this->branch->id,
                'day_of_week' => $day,
                'is_open' => ($day !== 7),
                'open_time' => '08:00:00',
                'close_time' => '12:00:00',
                'slot_duration_minutes' => 60,
                'capacity_per_slot' => 1,
            ]);
        }
    }

    public function test_race_condition_slot_protection(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::MONDAY)->format('Y-m-d');

        $appt1 = $this->slotService->book([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '08:00',
            'patient_name' => 'Pasien A',
            'patient_email' => 'pasien.a@example.com',
            'patient_phone' => '08111111111',
            'patient_age' => 30,
            'complaint' => 'Keluhan A',
        ]);

        $this->assertNotNull($appt1);

        // Booking kedua pada slot yang sama persis melempar ValidationException
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Slot sudah terisi, silakan pilih jam lain.');

        $this->slotService->book([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '08:00',
            'patient_name' => 'Pasien B',
            'patient_email' => 'pasien.b@example.com',
            'patient_phone' => '08222222222',
            'patient_age' => 32,
            'complaint' => 'Keluhan B',
        ]);
    }

    public function test_patient_manage_route_returns_404(): void
    {
        // Pasien tidak punya aksi lanjutan setelah membuat janji
        $response = $this->get('/janji/SAMXYZ12345');
        $response->assertStatus(404);
    }

    public function test_confirmation_page_has_no_cancel_or_reschedule_buttons(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::TUESDAY)->format('Y-m-d');
        $appointment = Appointment::create([
            'code' => 'SAMCONF12345',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Budi Santoso',
            'patient_email' => 'budi@example.com',
            'patient_phone' => '628123456789',
            'patient_age' => 45,
            'complaint' => 'Drop Foot Kiri Sangat Sensitif',
            'status' => 'confirmed',
        ]);

        $response = $this->get(route('appointment.confirmation', $appointment->code));

        $response->assertStatus(200);
        $response->assertSee('SAMCONF12345');
        $response->assertSee('Budi Santoso');
        // Tidak ada tombol aksi pembatalan atau perubahan jadwal untuk pasien
        $response->assertDontSee('Batalkan Janji');
        $response->assertDontSee('Ubah Jadwal');
        // Ada teks info kontak untuk perubahan/pembatalan via admin
        $response->assertSee('Untuk perubahan atau pembatalan, silakan hubungi admin kami');
        $response->assertSee('WhatsApp Cabang:');
    }

    public function test_prescription_access_without_login_is_rejected(): void
    {
        $appointment = Appointment::create([
            'code' => 'SAMRX1234567',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => Carbon::now('Asia/Jakarta')->next(Carbon::WEDNESDAY)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'patient_name' => 'Rahasia Medis',
            'patient_email' => 'rahasia@example.com',
            'patient_phone' => '628999999999',
            'patient_age' => 50,
            'complaint' => 'Rahasia',
            'prescription_path' => 'prescriptions/resep_rahasia.pdf',
            'status' => 'pending',
        ]);

        Storage::disk('local')->put('prescriptions/resep_rahasia.pdf', '%PDF-1.4 test');

        $response = $this->get(route('filament.kelola-sam.prescription.download', $appointment));
        $response->assertRedirect();
        $this->assertStringContainsString('kelola-sam/login', $response->headers->get('Location'));
    }
}
