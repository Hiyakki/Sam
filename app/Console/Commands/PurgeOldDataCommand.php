<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeOldDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sam:purge-old-data 
                            {--months= : Periode retensi data dalam bulan (default dari config)}
                            {--dry-run : Menjalankan simulasi pembersihan tanpa menghapus file atau memodifikasi database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus file resep dan menganonimkan data pasien pada janji lama berstatus completed/cancelled/no_show sesuai kebijakan retensi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $months = (int) ($this->option('months') ?: config('app.data_retention_months', 24));
        $dryRun = (bool) $this->option('dry-run');

        $cutoffDate = Carbon::now('Asia/Jakarta')->subMonths($months)->startOfDay();

        $this->info("Menjalankan pembersihan data janji temu lebih lama dari {$months} bulan (sebelum {$cutoffDate->format('Y-m-d')})...");
        if ($dryRun) {
            $this->warn('Mode: DRY-RUN (simulasi aktif, tidak ada data atau file yang dihapus).');
        }

        $query = Appointment::whereIn('status', ['completed', 'cancelled', 'no_show'])
            ->whereDate('appointment_date', '<', $cutoffDate->format('Y-m-d'))
            ->where(function ($q) {
                $q->where('patient_name', '!=', 'Pasien Anonim')
                    ->orWhereNotNull('prescription_path');
            });

        $totalRecords = $query->count();
        $this->line("Ditemukan {$totalRecords} janji temu lama untuk diproses.");

        if ($totalRecords === 0) {
            $this->info('Tidak ada data janji temu yang perlu dibersihkan.');

            return self::SUCCESS;
        }

        $anonymizedCount = 0;
        $filesDeletedCount = 0;

        $query->chunkById(100, function ($appointments) use ($dryRun, &$anonymizedCount, &$filesDeletedCount) {
            foreach ($appointments as $appointment) {
                if (filled($appointment->prescription_path)) {
                    if (Storage::disk('local')->exists($appointment->prescription_path)) {
                        if (! $dryRun) {
                            Storage::disk('local')->delete($appointment->prescription_path);
                        }
                        $filesDeletedCount++;
                    }
                }

                if (! $dryRun) {
                    $appointment->update([
                        'patient_name' => 'Pasien Anonim',
                        'patient_phone' => '620000000000',
                        'patient_email' => 'anonim@sam.internal',
                        'complaint' => '[Data dianonimkan sesuai kebijakan retensi data]',
                        'prescription_path' => null,
                    ]);
                }

                $anonymizedCount++;
            }
        });

        $prefix = $dryRun ? '[DRY-RUN] Berhasil mensimulasikan' : 'Berhasil mengeksekusi';
        $this->info("{$prefix}: {$anonymizedCount} data janji temu dianonimkan dan {$filesDeletedCount} file resep dokter dibersihkan.");

        return self::SUCCESS;
    }
}
