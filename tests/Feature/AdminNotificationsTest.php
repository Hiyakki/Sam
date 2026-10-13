<?php

namespace Tests\Feature;

use App\Mail\DailyAgendaAdminMail;
use App\Mail\TestimonialPendingAdminMail;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Services\TestimonialSubmissionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminNotificationsTest extends TestCase
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

    public function test_submitting_testimonial_queues_email_to_admin(): void
    {
        Mail::fake();

        $service = app(TestimonialSubmissionService::class);
        $testimonial = $service->submit([
            'name' => 'Ibu Maria',
            'role_or_info' => 'Pasien Kaki Palsu',
            'content' => 'Pelayanan sangat memuaskan dan presisi.',
            'rating' => 5,
            'consent_confirmed' => true,
        ]);

        $this->assertEquals('pending', $testimonial->status);

        Mail::assertQueued(TestimonialPendingAdminMail::class, function ($mail) use ($testimonial) {
            return $mail->testimonial->id === $testimonial->id;
        });
    }

    public function test_daily_agenda_command_sends_email_when_appointments_exist_today(): void
    {
        Mail::fake();

        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        // Buat janji temu untuk hari ini
        Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $today,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Pasien Hari Ini',
            'patient_email' => 'hariini@test.com',
            'patient_phone' => '6281234567890',
            'patient_age' => 30,
            'complaint' => 'Konsultasi pagi',
            'status' => 'confirmed',
        ]);

        $this->artisan('appointment:send-daily-summary')
            ->expectsOutputToContain('Berhasil mengirim email agenda harian (1 janji temu)')
            ->assertSuccessful();

        Mail::assertQueued(DailyAgendaAdminMail::class, function ($mail) {
            return $mail->appointments->count() === 1 && $mail->appointments->first()->patient_name === 'Pasien Hari Ini';
        });
    }

    public function test_daily_agenda_command_skips_when_no_appointments_today(): void
    {
        Mail::fake();

        // Tidak ada janji temu hari ini (hanya besok)
        Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => Carbon::tomorrow('Asia/Jakarta')->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Pasien Besok',
            'patient_email' => 'besok@test.com',
            'patient_phone' => '6281234567890',
            'patient_age' => 30,
            'complaint' => 'Konsultasi besok',
            'status' => 'confirmed',
        ]);

        $this->artisan('appointment:send-daily-summary')
            ->expectsOutputToContain('Tidak ada janji temu aktif untuk hari ini')
            ->assertSuccessful();

        Mail::assertNotQueued(DailyAgendaAdminMail::class);
    }
}
