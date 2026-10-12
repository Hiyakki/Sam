<?php

namespace App\Console\Commands;

use App\Mail\DailyAgendaAdminMail;
use App\Models\Appointment;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyAgendaSummaryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointment:send-daily-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email ringkasan agenda harian pukul 07:30 WIB jika ada janji temu aktif hari ini';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        $appointments = Appointment::whereDate('appointment_date', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->with(['branch', 'service'])
            ->orderBy('start_time')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info("Tidak ada janji temu aktif untuk hari ini ({$today}). Email ringkasan dilewati.");

            return self::SUCCESS;
        }

        $adminEmail = SiteSetting::get('company.email', config('mail.from.address'));

        if (blank($adminEmail)) {
            $this->warn('Email tujuan admin tidak terkonfigurasi di pengaturan website.');

            return self::FAILURE;
        }

        Mail::to($adminEmail)->queue(new DailyAgendaAdminMail($appointments, $today));

        $this->info("Berhasil mengirim email agenda harian ({$appointments->count()} janji temu) ke {$adminEmail}.");

        return self::SUCCESS;
    }
}
