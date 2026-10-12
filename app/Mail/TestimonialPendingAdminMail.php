<?php

namespace App\Mail;

use App\Models\Testimonial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestimonialPendingAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Testimonial $testimonial) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[SAM] Testimoni Baru Menunggu Moderasi - {$this->testimonial->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.testimonial-pending-admin',
        );
    }
}
