<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppSender;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteSender implements WhatsAppSender
{
    public function __construct(
        protected string $token,
        protected string $endpoint = 'https://api.fonnte.com/send'
    ) {}

    /**
     * Kirim pesan melalui API gateway Fonnte secara fail-safe.
     */
    public function send(string $to, string $message): bool
    {
        $normalizedPhone = $this->normalizePhone($to);

        if (blank($this->token) || blank($normalizedPhone)) {
            Log::warning('WhatsApp Fonnte: Token gateway atau nomor tujuan kosong.');

            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $this->token,
                ])
                ->post($this->endpoint, [
                    'target' => $normalizedPhone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $maskedPhone = strlen($normalizedPhone) > 6 ? substr($normalizedPhone, 0, 4).'****'.substr($normalizedPhone, -2) : '****';

            if ($response->successful()) {
                Log::info("WhatsApp Fonnte: Berhasil mengirim pesan ke {$maskedPhone}");

                return true;
            }

            Log::warning("WhatsApp Fonnte: Gagal mengirim pesan ke {$maskedPhone}. Status: {$response->status()}");

            return false;
        } catch (Throwable $e) {
            // Fail-safe mutlak: jangan pernah menggagalkan alur aplikasi
            Log::error("WhatsApp Fonnte Exception: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Normalisasi nomor HP ke format 62xxx.
     */
    protected function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (str_starts_with($cleaned, '0')) {
            return '62'.substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '62')) {
            return $cleaned;
        }

        return '62'.$cleaned;
    }
}
