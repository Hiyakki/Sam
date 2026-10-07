<?php

namespace Tests\Feature;

use App\Mail\AppointmentCreatedAdminMail;
use App\Mail\AppointmentCreatedPatientMail;
use App\Mail\AppointmentReminderPatientMail;
use App\Mail\AppointmentRescheduledPatientMail;
use App\Mail\AppointmentStatusChangedPatientMail;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Phase7NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        SiteSetting::set('company.email', 'pusat@sam-orthotic.com');

        $this->branch = Branch::create([
            'name' => 'SAM Cabang Solo',
            'slug' => 'sam-solo',
            'address' => 'Jl. Veteran No. 12, Surakarta',
            'phone_wa' => '081234567890',
            'email' => 'solo@sam-orthotic.com',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name' => 'Konsultasi Ortotik',
            'slug' => 'konsultasi-ortotik',
            'is_active' => true,
        ]);

        $this->branch->services()->attach($this->service->id);
    }

    public function test_appointment_created_queues_email_to_patient_and_admin(): void
    {
        $appointment = Appointment::create([
            'code' => 'SAMNOTIF01',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => '2026-10-20',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Budi Susanto',
            'patient_email' => 'budi.susanto@example.com',
            'patient_phone' => '628123456789',
            'patient_age' => 35,
            'complaint' => 'Pemeriksaan rutin kaki palsu',
            'status' => 'pending',
        ]);

        // 1. Cek email pasien terantre
        Mail::assertQueued(AppointmentCreatedPatientMail::class, function ($mail) use ($appointment) {
            return $mail->hasTo('budi.susanto@example.com')
                && $mail->appointment->id === $appointment->id;
        });

        // 2. Cek email admin terantre ke email cabang/pusat
        Mail::assertQueued(AppointmentCreatedAdminMail::class, function ($mail) use ($appointment) {
            $envelope = $mail->envelope();

            return $mail->hasTo('solo@sam-orthotic.com')
                && str_contains($envelope->subject, 'SAM Cabang Solo')
                && $mail->appointment->id === $appointment->id;
        });
    }

    public function test_appointment_status_changed_queues_email_to_patient(): void
    {
        $appointment = Appointment::create([
            'code' => 'SAMNOTIF02',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => '2026-10-21',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'patient_name' => 'Ratna Sari',
            'patient_email' => 'ratna@example.com',
            'patient_phone' => '628123456788',
            'patient_age' => 28,
            'complaint' => 'Brace skoliosis',
            'status' => 'pending',
        ]);

        // Reset mail assertions dari event created
        Mail::fake();

        // Admin mengonfirmasi janji temu
        $appointment->update(['status' => 'confirmed']);

        Mail::assertQueued(AppointmentStatusChangedPatientMail::class, function ($mail) use ($appointment) {
            $envelope = $mail->envelope();

            return $mail->hasTo('ratna@example.com')
                && str_contains($envelope->subject, 'Terkonfirmasi')
                && $mail->appointment->id === $appointment->id;
        });
    }

    public function test_appointment_rescheduled_queues_email_to_patient(): void
    {
        $appointment = Appointment::create([
            'code' => 'SAMNOTIF03',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => '2026-10-22',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'patient_name' => 'Hendra Setiawan',
            'patient_email' => 'hendra@example.com',
            'patient_phone' => '628123456787',
            'patient_age' => 45,
            'complaint' => 'Konsultasi prosthetic',
            'status' => 'confirmed',
        ]);

        Mail::fake();

        // Pasien atau admin mengubah jadwal
        $appointment->update([
            'appointment_date' => '2026-10-25',
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
        ]);

        Mail::assertQueued(AppointmentRescheduledPatientMail::class, function ($mail) use ($appointment) {
            return $mail->hasTo('hendra@example.com')
                && $mail->appointment->id === $appointment->id;
        });
    }

    public function test_h_minus_1_reminder_command_queues_email_for_confirmed_appointments(): void
    {
        $tomorrow = Carbon::tomorrow('Asia/Jakarta')->format('Y-m-d');
        $dayAfterTomorrow = Carbon::tomorrow('Asia/Jakarta')->addDay()->format('Y-m-d');

        // Janji 1: Memenuhi syarat (besok, confirmed, belum dikirim pengingat)
        $apptTarget = Appointment::create([
            'code' => 'SAMREMIND01',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $tomorrow,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Target Pasien',
            'patient_email' => 'target@example.com',
            'patient_phone' => '628111111111',
            'patient_age' => 30,
            'complaint' => 'Fitting socket',
            'status' => 'confirmed',
            'reminder_sent_at' => null,
        ]);

        // Janji 2: Besok tapi masih PENDING (tidak boleh dikirimi reminder)
        $apptPending = Appointment::create([
            'code' => 'SAMREMIND02',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $tomorrow,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'patient_name' => 'Pending Pasien',
            'patient_email' => 'pending@example.com',
            'patient_phone' => '628222222222',
            'patient_age' => 32,
            'complaint' => 'Keluhan drop foot',
            'status' => 'pending',
            'reminder_sent_at' => null,
        ]);

        // Janji 3: Besok, confirmed, tapi SUDAH pernah dikirim pengingat
        $apptSent = Appointment::create([
            'code' => 'SAMREMIND03',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $tomorrow,
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
            'patient_name' => 'Sudah Dikirim',
            'patient_email' => 'sent@example.com',
            'patient_phone' => '628333333333',
            'patient_age' => 40,
            'complaint' => 'Keluhan',
            'status' => 'confirmed',
            'reminder_sent_at' => Carbon::yesterday('Asia/Jakarta'),
        ]);

        // Janji 4: LUSA (belum waktunya H-1)
        $apptLusa = Appointment::create([
            'code' => 'SAMREMIND04',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $dayAfterTomorrow,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Lusa Pasien',
            'patient_email' => 'lusa@example.com',
            'patient_phone' => '628444444444',
            'patient_age' => 50,
            'complaint' => 'Keluhan',
            'status' => 'confirmed',
            'reminder_sent_at' => null,
        ]);

        Mail::fake();

        // Jalankan artisan command pengingat H-1
        $this->artisan('appointment:send-reminders')
            ->expectsOutputToContain('Berhasil memproses dan mengantrekan 1 email pengingat H-1.')
            ->assertExitCode(0);

        // Pastikan hanya apptTarget yang menerima email pengingat
        Mail::assertQueued(AppointmentReminderPatientMail::class, 1);
        Mail::assertQueued(AppointmentReminderPatientMail::class, function ($mail) use ($apptTarget) {
            return $mail->hasTo('target@example.com')
                && $mail->appointment->id === $apptTarget->id;
        });

        // Pastikan kolom reminder_sent_at terisi
        $this->assertNotNull($apptTarget->fresh()->reminder_sent_at);
        $this->assertNull($apptPending->fresh()->reminder_sent_at);
        $this->assertNull($apptLusa->fresh()->reminder_sent_at);
    }

    public function test_all_mailables_implement_should_queue(): void
    {
        $dummy = Appointment::make(['code' => 'TEST']);

        $this->assertInstanceOf(ShouldQueue::class, new AppointmentCreatedPatientMail($dummy));
        $this->assertInstanceOf(ShouldQueue::class, new AppointmentCreatedAdminMail($dummy));
        $this->assertInstanceOf(ShouldQueue::class, new AppointmentStatusChangedPatientMail($dummy));
        $this->assertInstanceOf(ShouldQueue::class, new AppointmentRescheduledPatientMail($dummy));
        $this->assertInstanceOf(ShouldQueue::class, new AppointmentReminderPatientMail($dummy));
    }
}
