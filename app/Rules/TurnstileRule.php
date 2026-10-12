<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class TurnstileRule implements ValidationRule
{
    /**
     * Jalankan validasi Cloudflare Turnstile / captcha.
     * Otomatis lolos (nonaktif) bila secret key kosong di konfigurasi/env.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.turnstile.secret');

        if (blank($secret)) {
            return;
        }

        if (blank($value)) {
            $fail('Verifikasi keamanan (captcha) wajib diselesaikan.');

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            if (! $response->successful() || ! $response->json('success')) {
                $fail('Verifikasi keamanan (captcha) tidak valid atau telah kedaluwarsa.');
            }
        } catch (\Throwable $e) {
            report($e);
            $fail('Layanan verifikasi captcha sedang tidak dapat dihubungi. Silakan coba beberapa saat lagi.');
        }
    }
}
