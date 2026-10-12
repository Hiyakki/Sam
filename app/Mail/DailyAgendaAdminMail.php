<?php

namespace App\Mail;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyAgendaAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<Appointment>  $appointments
     */
    public function __construct(
        public Collection $appointments,
        public string $date,
    ) {}

    public function envelope(): Envelope
    {
        $formattedDate = Carbon::parse($this->date)->locale('id')->translatedFormat('l, d F Y');

        return new Envelope(
            subject: "[SAM] Agenda Kunjungan Pasien Hari Ini - {$formattedDate} ({$this->appointments->count()} Janji Temu)",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-agenda-admin',
        );
    }
}
