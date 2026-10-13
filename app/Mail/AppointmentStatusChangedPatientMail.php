<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusChangedPatientMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function envelope(): Envelope
    {
        $statusLabels = [
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Terkonfirmasi',
            'completed' => 'Kunjungan Selesai',
            'cancelled' => 'Dibatalkan',
            'no_show' => 'Tidak Hadir',
        ];
        $statusLabel = $statusLabels[$this->appointment->status] ?? ucfirst($this->appointment->status);

        return new Envelope(
            subject: "[SAM] Status Janji Temu {$this->appointment->patient_name}: {$statusLabel}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-status-changed-patient',
        );
    }
}
