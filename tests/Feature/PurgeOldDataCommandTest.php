<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurgeOldDataCommandTest extends TestCase
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
    }

    public function test_purge_command_deletes_prescription_files_and_anonymizes_old_completed_appointments(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('resep_lama.pdf', 100, 'application/pdf');
        $filePath = Storage::disk('local')->putFile('prescriptions', $file);
        $this->assertTrue(Storage::disk('local')->exists($filePath));

        // Janji temu 25 bulan lalu berstatus completed
        $oldDate = Carbon::now('Asia/Jakarta')->subMonths(25)->format('Y-m-d');
        $oldAppointment = Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $oldDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Budi Lama',
            'patient_email' => 'budi.lama@test.com',
            'patient_phone' => '6281234567890',
            'patient_age' => 45,
            'complaint' => 'Keluhan masa lalu',
            'prescription_path' => $filePath,
            'status' => 'completed',
        ]);

        // Janji temu baru (3 bulan lalu) tidak boleh disentuh
        $recentDate = Carbon::now('Asia/Jakarta')->subMonths(3)->format('Y-m-d');
        $recentAppointment = Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $recentDate,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'patient_name' => 'Pasien Baru',
            'patient_email' => 'pasien.baru@test.com',
            'patient_phone' => '6281999999999',
            'patient_age' => 25,
            'complaint' => 'Keluhan aktif',
            'prescription_path' => null,
            'status' => 'completed',
        ]);

        $this->artisan('sam:purge-old-data')
            ->assertSuccessful();

        // Verifikasi file resep terhapus dari disk
        $this->assertFalse(Storage::disk('local')->exists($filePath));

        // Verifikasi data pasien pada janji lama dianonimkan
        $oldAppointment->refresh();
        $this->assertEquals('Pasien Anonim', $oldAppointment->patient_name);
        $this->assertEquals('anonim@sam.internal', $oldAppointment->patient_email);
        $this->assertEquals('620000000000', $oldAppointment->patient_phone);
        $this->assertNull($oldAppointment->prescription_path);
        $this->assertStringContainsString('dianonimkan', $oldAppointment->complaint);

        // Verifikasi janji temu baru tidak berubah
        $recentAppointment->refresh();
        $this->assertEquals('Pasien Baru', $recentAppointment->patient_name);
        $this->assertEquals('pasien.baru@test.com', $recentAppointment->patient_email);
    }

    public function test_purge_command_dry_run_does_not_modify_anything(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('resep_dry.pdf', 100, 'application/pdf');
        $filePath = Storage::disk('local')->putFile('prescriptions', $file);

        $oldDate = Carbon::now('Asia/Jakarta')->subMonths(30)->format('Y-m-d');
        $appointment = Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $oldDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Data Asli Dry Run',
            'patient_email' => 'asli@test.com',
            'patient_phone' => '6281111222233',
            'patient_age' => 50,
            'complaint' => 'Keluhan asli',
            'prescription_path' => $filePath,
            'status' => 'cancelled',
        ]);

        $this->artisan('sam:purge-old-data --dry-run')
            ->expectsOutputToContain('DRY-RUN')
            ->assertSuccessful();

        // File tetap ada di disk
        $this->assertTrue(Storage::disk('local')->exists($filePath));

        // Data tidak dianonimkan
        $appointment->refresh();
        $this->assertEquals('Data Asli Dry Run', $appointment->patient_name);
        $this->assertEquals($filePath, $appointment->prescription_path);
    }
}
