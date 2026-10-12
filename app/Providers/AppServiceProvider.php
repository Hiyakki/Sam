<?php

namespace App\Providers;

use App\Console\Commands\SendAppointmentRemindersCommand;
use App\Contracts\WhatsAppSender;
use App\Models\Appointment;
use App\Observers\AppointmentObserver;
use App\Services\WhatsApp\FonnteSender;
use App\Services\WhatsApp\NullSender;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WhatsAppSender::class, function () {
            $config = config('services.whatsapp', []);

            if (! empty($config['enabled']) && ($config['driver'] ?? '') === 'fonnte') {
                return new FonnteSender(
                    token: (string) ($config['token'] ?? ''),
                    endpoint: (string) ($config['endpoint'] ?? 'https://api.fonnte.com/send')
                );
            }

            return new NullSender;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Appointment::observe(AppointmentObserver::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                SendAppointmentRemindersCommand::class,
            ]);
        }

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        RateLimiter::for('appointment.submission', function (Request $request) {
            $rawPhone = (string) $request->input('patient_phone', '');
            $cleaned = preg_replace('/[^0-9]/', '', $rawPhone) ?? '';
            $normalizedPhone = str_starts_with($cleaned, '0')
                ? '62'.substr($cleaned, 1)
                : (str_starts_with($cleaned, '62') ? $cleaned : '62'.$cleaned);

            return [
                Limit::perHour(3)->by($request->ip())->response(function () {
                    return back()->withInput()->withErrors([
                        'rate_limit' => 'Terlalu banyak pengajuan janji temu dari perangkat Anda. Silakan coba kembali dalam 1 jam.',
                    ]);
                }),
                Limit::perHour(3)->by('phone:'.$normalizedPhone)->response(function () {
                    return back()->withInput()->withErrors([
                        'patient_phone' => 'Nomor WhatsApp Anda telah mencapai batas maksimal 3 pengajuan dalam 1 jam. Silakan coba beberapa saat lagi.',
                    ]);
                }),
            ];
        });

        RateLimiter::for('appointment.confirmation', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });
    }
}
