<?php

namespace App\Console\Commands;

use App\Contracts\WhatsAppSender;
use App\Mail\AppointmentReminderPatientMail;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointment:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email pengingat H-1 untuk janji temu pasien yang terkonfirmasi besok hari';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tomorrowString = Carbon::tomorrow('Asia/Jakarta')->format('Y-m-d');

        $appointments = Appointment::where('status', 'confirmed')
            ->whereDate('appointment_date', $tomorrowString)
            ->whereNull('reminder_sent_at')
            ->with(['branch', 'service'])
            ->get();

        $this->info("Menemukan {$appointments->count()} janji temu terkonfirmasi untuk tanggal {$tomorrowString}.");

        $count = 0;
        foreach ($appointments as $appointment) {
            if (filled($appointment->patient_email)) {
                Mail::to($appointment->patient_email)->queue(new AppointmentReminderPatientMail($appointment));
            }

            // Kirim WhatsApp pengingat jika gateway aktif
            if (config('services.whatsapp.enabled') && filled($appointment->patient_phone)) {
                try {
                    $waSender = app(WhatsAppSender::class);
                    $branchName = $appointment->branch?->name ?? 'SAM';
                    $timeStr = substr((string) $appointment->start_time, 0, 5);
                    $msg = "Halo {$appointment->patient_name}, ini pengingat dari {$branchName}. Janji temu Anda terkonfirmasi untuk BESOK pukul {$timeStr} WIB. Mohon hadir 10 menit lebih awal.";
                    $waSender->send($appointment->patient_phone, $msg);
                } catch (\Throwable $e) {
                    Log::warning("Gagal mengirim WA pengingat H-1: {$e->getMessage()}");
                }
            }

            $appointment->update([
                'reminder_sent_at' => Carbon::now('Asia/Jakarta'),
            ]);

            $count++;
        }

        $this->info("Berhasil memproses dan mengantrekan {$count} email pengingat H-1.");

        return Command::SUCCESS;
    }
}
