<?php

namespace App\Observers;

use App\Mail\AppointmentCreatedAdminMail;
use App\Mail\AppointmentCreatedPatientMail;
use App\Mail\AppointmentRescheduledPatientMail;
use App\Mail\AppointmentStatusChangedPatientMail;
use App\Models\Appointment;
use App\Models\SiteSetting;
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
