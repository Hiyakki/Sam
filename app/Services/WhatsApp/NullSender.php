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
        $masked = strlen($to) > 6 ? substr($to, 0, 4).'****'.substr($to, -2) : '****';
        Log::info("WhatsApp NullSender: Pesan disimulasikan ke {$masked} (panjang: ".strlen($message).' karakter)');

        return true;
    }
}
