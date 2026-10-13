<?php

namespace Tests\Feature;

use App\Filament\Resources\AppointmentResource;
use App\Filament\Resources\AppointmentResource\Pages\ListAppointments;
use App\Mail\AppointmentCreatedAdminMail;
use App\Mail\AppointmentCreatedPatientMail;
use App\Mail\AppointmentReminderPatientMail;
use App\Mail\AppointmentRescheduledPatientMail;
use App\Mail\AppointmentStatusChangedPatientMail;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class BookingCodeRemovalTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;

    protected Service $service;

    protected Appointment $appointment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'SAM Cabang Solo',
            'slug' => 'sam-solo',
            'address' => 'Jl. Veteran No. 12, Surakarta',
            'phone_wa' => '081234567890',
            'email' => 'solo@sam.test',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name' => 'Konsultasi Ortotik Kaki',
            'slug' => 'konsultasi-ortotik',
            'is_active' => true,
        ]);

        $this->appointment = Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => Carbon::tomorrow('Asia/Jakarta')->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Budi Santoso',
            'patient_email' => 'budi@example.com',
            'patient_phone' => '6289999888877',
            'patient_age' => 35,
            'complaint' => 'Nyeri ankle dan drop foot akut',
            'prescription_path' => 'prescriptions/resep1.pdf',
            'status' => 'pending',
        ]);
    }

    public function test_appointments_table_has_no_code_column(): void
    {
        $this->assertFalse(
            Schema::hasColumn('appointments', 'code'),
            'Tabel appointments seharusnya tidak memiliki kolom code.'
        );
    }

    public function test_old_confirmation_route_returns_404(): void
    {
        $this->get('/janji/konfirmasi/SAM-123456')->assertStatus(404);
        $this->get('/janji/konfirmasi/apa-saja')->assertStatus(404);
        $this->get('/janji/SAM-123456')->assertStatus(404);
    }

    public function test_success_route_with_session_displays_minimal_summary(): void
    {
        $response = $this->withSession(['appointment_success_id' => $this->appointment->id])
            ->get(route('appointment.success'));

        $response->assertStatus(200);
        $response->assertSee('Janji Temu Anda Telah Terdaftar');
        $response->assertSee('SAM Cabang Solo');
        $response->assertSee('Konsultasi Ortotik Kaki');
        $response->assertSee('Menunggu Konfirmasi Admin');
        $response->assertSee('081234567890'); // Kontak WA cabang

        // Tidak boleh menampilkan kode, nomor lengkap pasien, keluhan, atau file resep
        $response->assertDontSee('Kode Janji');
        $response->assertDontSee('Kode Booking');
        $response->assertDontSee('Nyeri ankle dan drop foot akut');
        $response->assertDontSee('resep1.pdf');
        $response->assertDontSee('6289999888877'); // Nomor telepon lengkap pasien disembunyikan

        // Header SEO noindex
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_success_route_without_session_displays_generic_thank_you(): void
    {
        $response = $this->get(route('appointment.success'));

        $response->assertStatus(200);
        $response->assertSee('Terima Kasih');
        $response->assertSee('Kembali ke Beranda');
        $response->assertSee(route('home'));

        // Tidak menampilkan data janji spesifik
        $response->assertDontSee('Menunggu Konfirmasi Admin');
        $response->assertDontSee('SAM Cabang Solo');
        $response->assertDontSee('Konsultasi Ortotik Kaki');

        // Header SEO noindex
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_emails_do_not_contain_code(): void
    {
        $mailables = [
            new AppointmentCreatedPatientMail($this->appointment),
            new AppointmentCreatedAdminMail($this->appointment),
            new AppointmentStatusChangedPatientMail($this->appointment),
            new AppointmentRescheduledPatientMail($this->appointment),
            new AppointmentReminderPatientMail($this->appointment),
        ];

        foreach ($mailables as $mailable) {
            $envelope = $mailable->envelope();
            $this->assertStringNotContainsStringIgnoringCase('kode', $envelope->subject, 'Subject email tidak boleh memuat kata "kode".');
            $this->assertStringNotContainsString('SAM-', $envelope->subject, 'Subject email tidak boleh memuat prefix kode.');

            $html = $mailable->render();
            $this->assertStringNotContainsStringIgnoringCase('kode janji', $html, 'Isi email tidak boleh memuat "kode janji".');
            $this->assertStringNotContainsStringIgnoringCase('kode booking', $html, 'Isi email tidak boleh memuat "kode booking".');
        }
    }

    public function test_admin_table_does_not_have_code_column(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->assertTableColumnDoesNotExist('code');
    }

    public function test_whatsapp_templates_do_not_contain_code(): void
    {
        $types = ['confirm', 'reminder', 'reschedule', 'cancel'];

        foreach ($types as $type) {
            $msg = AppointmentResource::generateTemplateMessage($this->appointment, $type);
            $this->assertStringNotContainsStringIgnoringCase('kode', $msg, "Template WA '{$type}' tidak boleh memuat kata 'kode'.");
            $this->assertStringNotContainsString('SAM-', $msg, "Template WA '{$type}' tidak boleh memuat format kode.");
        }
    }
}
