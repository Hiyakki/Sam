<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\BranchClosure;
use App\Models\BranchSchedule;
use App\Models\Service;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase6AppointmentFormTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        SiteSetting::set('general.appointment_max_days', '60');
        SiteSetting::set('general.appointment_reschedule_min_hours', '24');
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

        // Jadwal Senin - Sabtu
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

    public function test_appointment_page_renders_successfully(): void
    {
        $response = $this->get(route('appointment'));

        $response->assertStatus(200);
        $response->assertSee('Buat Janji Temu');
        $response->assertSee('SAM Cabang Solo');
    }

    public function test_api_branches_services_returns_active_services(): void
    {
        $response = $this->getJson(route('api.appointment.services', $this->branch->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $this->service->id,
            'name' => 'Konsultasi Ortotik Kustom',
        ]);
    }

    public function test_api_closed_dates_returns_closures(): void
    {
        $closureDate = Carbon::now('Asia/Jakarta')->addDays(5)->format('Y-m-d');
        BranchClosure::create([
            'branch_id' => $this->branch->id,
            'date' => $closureDate,
            'reason' => 'Libur Cabang',
            'is_closed' => true,
        ]);

        $response = $this->getJson(route('api.appointment.closed-dates', ['branch_id' => $this->branch->id]));

        $response->assertStatus(200);
        $this->assertContains($closureDate, $response->json('closed_dates'));
        $this->assertContains(7, $response->json('closed_days_of_week'));
    }

    public function test_api_slots_returns_available_slots(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::MONDAY)->format('Y-m-d');

        $response = $this->getJson(route('api.appointment.slots', [
            'branch_id' => $this->branch->id,
            'date' => $targetDate,
        ]));

        $response->assertStatus(200);
        $this->assertCount(4, $response->json('slots')); // 08:00 - 12:00 (4 slot)
        $this->assertEquals('08:00', $response->json('slots.0.start'));
    }

    public function test_valid_appointment_submission_creates_record_and_redirects(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::TUESDAY)->format('Y-m-d');

        $response = $this->post(route('appointment.store'), [
            'patient_name' => 'Ahmad Dahlan',
            'patient_email' => 'ahmad@example.com',
            'patient_phone' => '081234567890',
            'patient_age' => 40,
            'complaint' => 'Butuh kaki palsu setelah amputasi',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '08:00',
            'privacy_consent' => '1',
            'website_hp' => '',
        ]);

        $appointment = Appointment::where('patient_email', 'ahmad@example.com')->first();
        $this->assertNotNull($appointment);
        $this->assertEquals('pending', $appointment->status);
        $this->assertEquals('6281234567890', $appointment->patient_phone);

        $response->assertRedirect(route('appointment.confirmation', ['code' => $appointment->code]));
    }

    public function test_prescription_file_upload_is_stored_privately(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::WEDNESDAY)->format('Y-m-d');
        $file = UploadedFile::fake()->create('resep_dokter.pdf', 500, 'application/pdf');

        $this->post(route('appointment.store'), [
            'patient_name' => 'Budi Dokter',
            'patient_email' => 'budi.dokter@example.com',
            'patient_phone' => '081298765432',
            'patient_age' => 25,
            'complaint' => 'Rujukan dokter spesialis ortopedi',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '09:00',
            'prescription' => $file,
            'privacy_consent' => '1',
            'website_hp' => '',
        ]);

        $appointment = Appointment::where('patient_email', 'budi.dokter@example.com')->first();
        $this->assertNotNull($appointment);
        $this->assertNotNull($appointment->prescription_path);
        Storage::disk('local')->assertExists($appointment->prescription_path);
    }

    public function test_honeypot_submission_is_rejected(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::THURSDAY)->format('Y-m-d');

        $response = $this->post(route('appointment.store'), [
            'patient_name' => 'Spam Bot',
            'patient_email' => 'bot@example.com',
            'patient_phone' => '081234567890',
            'patient_age' => 30,
            'complaint' => 'Spam content',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '10:00',
            'privacy_consent' => '1',
            'website_hp' => 'I am a bot', // Field spam terisi
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('appointments', ['patient_name' => 'Spam Bot']);
    }

    public function test_active_appointment_limit_per_phone_number_is_enforced(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::FRIDAY)->format('Y-m-d');

        // Buat 2 janji aktif dengan nomor telepon yang sama
        Appointment::create([
            'code' => 'SAMTEST01',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'patient_name' => 'Pasien 1',
            'patient_email' => 'pasien1@example.com',
            'patient_phone' => '628999999999',
            'patient_age' => 30,
            'complaint' => 'Keluhan 1',
            'status' => 'pending',
        ]);

        Appointment::create([
            'code' => 'SAMTEST02',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Pasien 2',
            'patient_email' => 'pasien2@example.com',
            'patient_phone' => '628999999999',
            'patient_age' => 30,
            'complaint' => 'Keluhan 2',
            'status' => 'confirmed',
        ]);

        // Coba ajukan janji ke-3 dengan nomor yang sama
        $response = $this->post(route('appointment.store'), [
            'patient_name' => 'Pasien 3',
            'patient_email' => 'pasien3@example.com',
            'patient_phone' => '08999999999', // format 0899... dinormalisasi ke 62899...
            'patient_age' => 30,
            'complaint' => 'Keluhan 3',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '10:00',
            'privacy_consent' => '1',
            'website_hp' => '',
        ]);

        $response->assertSessionHasErrors('patient_phone');
    }

    public function test_patient_manage_route_does_not_exist(): void
    {
        // Keputusan produk: Pasien tidak punya aksi lanjutan / kelola janji via URL
        $response = $this->get('/janji/SAMTESTCODE123');
        $response->assertStatus(404);
    }

    public function test_confirmation_page_has_no_action_buttons_except_contact_info(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::MONDAY)->format('Y-m-d');
        $appointment = Appointment::create([
            'code' => 'SAMCONFIRM01',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'patient_name' => 'Bambang Tri',
            'patient_email' => 'bambang@example.com',
            'patient_phone' => '628123456789',
            'patient_age' => 50,
            'complaint' => 'Keluhan lutut',
            'status' => 'confirmed',
        ]);

        $response = $this->get(route('appointment.confirmation', $appointment->code));

        $response->assertStatus(200);
        $response->assertSee('SAMCONFIRM01');
        $response->assertSee('Bambang Tri');
        // Tidak boleh ada tombol kelola / ubah / batal untuk pasien
        $response->assertDontSee('Ubah Jadwal');
        $response->assertDontSee('Batalkan Janji');
        // Harus ada tombol / info kontak WA cabang
        $response->assertSee('Konfirmasi via WhatsApp');
    }

    public function test_honeypot_rejects_bot_submissions(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::MONDAY)->format('Y-m-d');

        $response = $this->post(route('appointment.store'), [
            'patient_name' => 'Spam Bot',
            'patient_email' => 'bot@example.com',
            'patient_phone' => '081234567890',
            'patient_age' => 30,
            'complaint' => 'Buy cheap medications',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '08:00',
            'privacy_consent' => '1',
            'website_hp' => 'I am a spam bot', // Terisi honeypot
        ]);

        $response->assertSessionHasErrors('website_hp');
    }
}
