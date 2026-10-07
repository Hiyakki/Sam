<?php

namespace App\Providers;

use App\Console\Commands\SendAppointmentRemindersCommand;
use App\Contracts\WhatsAppSender;
use App\Models\Appointment;
use App\Observers\AppointmentObserver;
use App\Services\WhatsApp\FonnteSender;
use App\Services\WhatsApp\NullSender;
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
    }
}
