<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable
    {
        return 'Masuk Admin SAM';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'SAM Orthotic Prosthetic — Masuk untuk mengelola data';
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title('Terlalu Banyak Percobaan Masuk')
            ->body("Terlalu banyak percobaan masuk yang gagal. Silakan coba kembali dalam {$exception->secondsUntilAvailable} detik.")
            ->danger();
    }
}
