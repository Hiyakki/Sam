<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentCreatedAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function envelope(): Envelope
    {
        $branchName = $this->appointment->branch?->name ?? 'Cabang SAM';

        return new Envelope(
            subject: "[Janji Temu Baru - {$branchName}] {$this->appointment->patient_name} ({$this->appointment->code})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-created-admin',
        );
    }
}
