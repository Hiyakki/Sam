<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppSender;
use Illuminate\Support\Facades\Log;

class NullSender implements WhatsAppSender
{
    /**
     * Driver default tanpa biaya. Mencatat pesan ke log aplikasi.
     */
    public function send(string $to, string $message): bool
    {
        Log::info("WhatsApp NullSender: Pesan ke {$to} disimulasikan: {$message}");

        return true;
    }
}
