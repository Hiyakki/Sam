<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminderPatientMail;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
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

            $appointment->update([
                'reminder_sent_at' => Carbon::now('Asia/Jakarta'),
            ]);

            $count++;
        }

        $this->info("Berhasil memproses dan mengantrekan {$count} email pengingat H-1.");

        return Command::SUCCESS;
    }
}
