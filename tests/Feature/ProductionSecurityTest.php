<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\BranchSchedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductionSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Solo',
            'slug' => 'solo',
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

        for ($d = 1; $d <= 7; $d++) {
            BranchSchedule::create([
                'branch_id' => $this->branch->id,
                'day_of_week' => $d,
                'is_open' => true,
                'open_time' => '08:00:00',
                'close_time' => '17:00:00',
                'slot_duration_minutes' => 60,
                'capacity_per_slot' => 2,
            ]);
        }
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_guest_cannot_download_prescription(): void
    {
        Storage::fake('local');
        $filePath = Storage::disk('local')->putFile('prescriptions', UploadedFile::fake()->create('resep.pdf', 100, 'application/pdf'));

        $appointment = Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Rahasia Pasien',
            'patient_email' => 'pasien@test.com',
            'patient_phone' => '6281234567890',
            'patient_age' => 40,
            'complaint' => 'Data keluhan sangat rahasia',
            'prescription_path' => $filePath,
            'status' => 'pending',
        ]);

        $downloadUrl = route('filament.kelola-sam.prescription.download', $appointment);

        // Guest mencoba download
        $response = $this->get($downloadUrl);
        $response->assertRedirect();
        $this->assertStringContainsString('kelola-sam/login', $response->headers->get('Location'));
    }

    public function test_authenticated_admin_can_download_prescription(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('resep.pdf', 100, 'application/pdf');
        $filePath = Storage::disk('local')->putFile('prescriptions', $file);

        $appointment = Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Pasien Sah',
            'patient_email' => 'pasien@test.com',
            'patient_phone' => '6281234567890',
            'patient_age' => 40,
            'complaint' => 'Pemeriksaan rutin',
            'prescription_path' => $filePath,
            'status' => 'pending',
        ]);

        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('filament.kelola-sam.prescription.download', $appointment));
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

    public function test_fake_mimetype_prescription_is_rejected(): void
    {
        // File dengan ekstensi .pdf tapi MIME aslinya text/plain (bukan application/pdf atau image)
        $fakeFile = UploadedFile::fake()->create('malicious.pdf', 10, 'text/plain');

        $payload = [
            'patient_name' => 'Test Hacker',
            'patient_email' => 'hacker@test.com',
            'patient_phone' => '081299998888',
            'patient_age' => 25,
            'complaint' => 'Keluhan',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '09:00',
            'privacy_consent' => '1',
            'website_hp' => '',
            'prescription' => $fakeFile,
        ];

        $response = $this->post(route('appointment.store'), $payload);
        $response->assertSessionHasErrors('prescription');
    }

    public function test_success_page_does_not_leak_sensitive_patient_data(): void
    {
        $appointment = Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Budi Sudarsono',
            'patient_email' => 'budi.rahasia@test.com',
            'patient_phone' => '6289912345678',
            'patient_age' => 45,
            'complaint' => 'Sangat sensitif: Amputasi paha kiri akibat trauma kecelakaan kerja',
            'prescription_path' => 'prescriptions/secret.pdf',
            'status' => 'pending',
        ]);

        $response = $this->withSession(['appointment_success_id' => $appointment->id])
            ->get(route('appointment.success'));

        $response->assertStatus(200);
        // Menampilkan ringkasan minimal
        $response->assertSee('Solo');

        // TIDAK boleh membocorkan data sensitif pasien atau kode
        $response->assertDontSee('Sangat sensitif: Amputasi paha kiri');
        $response->assertDontSee('secret.pdf');
        $response->assertDontSee('6289912345678');
        $response->assertDontSee('budi.rahasia@test.com');
    }
}
