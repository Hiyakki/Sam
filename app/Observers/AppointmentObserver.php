<?php

namespace App\Observers;

use App\Contracts\WhatsAppSender;
use App\Mail\AppointmentCreatedAdminMail;
use App\Mail\AppointmentCreatedPatientMail;
use App\Mail\AppointmentRescheduledPatientMail;
use App\Mail\AppointmentStatusChangedPatientMail;
use App\Models\Appointment;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $appointment->loadMissing(['branch', 'service']);

        // 1. Email konfirmasi ke pasien
        if (filled($appointment->patient_email)) {
            Mail::to($appointment->patient_email)->queue(new AppointmentCreatedPatientMail($appointment));
        }

        // 2. Email notifikasi ke admin cabang / perusahaan
        $adminEmail = $appointment->branch?->email
            ?: SiteSetting::get('company.email', config('mail.from.address'));

        if (filled($adminEmail)) {
            Mail::to($adminEmail)->queue(new AppointmentCreatedAdminMail($appointment));
        }

        // 3. Notifikasi WhatsApp ke admin bila gateway aktif
        if (config('services.whatsapp.enabled')) {
            try {
                $adminWa = $appointment->branch?->phone_wa ?: SiteSetting::get('company.whatsapp');
                if (filled($adminWa)) {
                    $waSender = app(WhatsAppSender::class);
                    $branchName = $appointment->branch?->name ?? 'SAM';
                    $dateStr = $appointment->appointment_date ? $appointment->appointment_date->format('d/m/Y') : '-';
                    $timeStr = substr((string) $appointment->start_time, 0, 5);
                    $msg = "Halo Admin {$branchName}, terdapat janji temu baru masuk:\nPasien: {$appointment->patient_name}\nWA: {$appointment->patient_phone}\nLayanan: {$appointment->service?->name}\nJadwal: {$dateStr} {$timeStr} WIB.";
                    $waSender->send($adminWa, $msg);
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal mengirim WA notifikasi: {$e->getMessage()}");
            }
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        $appointment->loadMissing(['branch', 'service']);

        // Jika hanya reminder_sent_at atau timestamps yang berubah, tidak perlu kirim email update
        $changedAttributes = array_keys($appointment->getChanges());
        $ignorableAttributes = ['reminder_sent_at', 'updated_at'];
        $meaningfulChanges = array_diff($changedAttributes, $ignorableAttributes);

        if (empty($meaningfulChanges)) {
            return;
        }

        // Jika jadwal kunjungan diubah
        if ($appointment->wasChanged(['appointment_date', 'start_time'])) {
            if (filled($appointment->patient_email)) {
                Mail::to($appointment->patient_email)->queue(new AppointmentRescheduledPatientMail($appointment));
            }

            return;
        }

        // Jika status reservasi diubah (misal: dikonfirmasi / dibatalkan)
        if ($appointment->wasChanged('status')) {
            if (filled($appointment->patient_email)) {
                Mail::to($appointment->patient_email)->queue(new AppointmentStatusChangedPatientMail($appointment));
            }
        }
    }
}
