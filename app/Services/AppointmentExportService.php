<?php

namespace App\Services;

use App\Exports\AppointmentExport;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppointmentExportService
{
    /**
     * Hitung perkiraan jumlah baris janji temu berdasarkan filter yang dipilih.
     *
     * @param  array<string, mixed>  $filters
     */
    public function countForFilters(array $filters): int
    {
        return $this->queryForFilters($filters)->count();
    }

    /**
     * Bangun query Eloquent berdasarkan filter dengan eager loading cabang dan layanan.
     *
     * @param  array<string, mixed>  $filters
     */
    public function queryForFilters(array $filters): Builder
    {
        $query = Appointment::query()
            ->with(['branch', 'service']);

        $periodBy = $filters['period_by'] ?? 'appointment_date';
        $dateBounds = $this->calculateDateBounds($filters);

        if (! empty($dateBounds) && ! ($dateBounds['is_all'] ?? false)) {
            $startDate = $dateBounds['start'] ?? null;
            $endDate = $dateBounds['end'] ?? null;

            if ($startDate !== null && $endDate !== null) {
                if ($periodBy === 'created_at') {
                    // Konversi waktu Asia/Jakarta ke UTC untuk pencocokan kolom timestamp created_at
                    $startUtc = $startDate->copy()->setTimezone('UTC');
                    $endUtc = $endDate->copy()->setTimezone('UTC');
                    $query->whereBetween('created_at', [$startUtc, $endUtc]);
                } else {
                    // Kolom date appointment_date (portabel untuk MySQL dan SQLite)
                    $query->whereDate('appointment_date', '>=', $startDate->format('Y-m-d'))
                        ->whereDate('appointment_date', '<=', $endDate->format('Y-m-d'));
                }
            } elseif ($startDate !== null) {
                if ($periodBy === 'created_at') {
                    $query->where('created_at', '>=', $startDate->copy()->setTimezone('UTC'));
                } else {
                    $query->whereDate('appointment_date', '>=', $startDate->format('Y-m-d'));
                }
            } elseif ($endDate !== null) {
                if ($periodBy === 'created_at') {
                    $query->where('created_at', '<=', $endDate->copy()->setTimezone('UTC'));
                } else {
                    $query->whereDate('appointment_date', '<=', $endDate->format('Y-m-d'));
                }
            }
        }

        // Filter status
        if (! empty($filters['statuses'])) {
            $query->whereIn('status', (array) $filters['statuses']);
        }

        // Filter kantor cabang
        if (! empty($filters['branch_ids'])) {
            $query->whereIn('branch_id', (array) $filters['branch_ids']);
        }

        // Filter layanan
        if (! empty($filters['service_ids'])) {
            $query->whereIn('service_id', (array) $filters['service_ids']);
        }

        // Urutkan berdasarkan tanggal lalu jam janji temu
        return $query->orderBy('appointment_date', 'asc')->orderBy('start_time', 'asc');
    }

    /**
     * Hitung batas awal dan akhir rentang waktu dalam zona waktu Asia/Jakarta.
     *
     * @param  array<string, mixed>  $filters
     * @return array{start: ?Carbon, end: ?Carbon, label: string, is_all: bool}
     */
    public function calculateDateBounds(array $filters): array
    {
        $range = $filters['date_range'] ?? 'all';
        $now = Carbon::now('Asia/Jakarta');

        return match ($range) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'Hari Ini ('.$now->format('d/m/Y').')',
                'is_all' => false,
            ],
            'this_week' => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
                'label' => 'Minggu Ini ('.$now->copy()->startOfWeek()->format('d/m/Y').' s/d '.$now->copy()->endOfWeek()->format('d/m/Y').')',
                'is_all' => false,
            ],
            'this_month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'label' => 'Bulan Ini ('.$now->copy()->startOfMonth()->format('d/m/Y').' s/d '.$now->copy()->endOfMonth()->format('d/m/Y').')',
                'is_all' => false,
            ],
            'last_month' => [
                'start' => $now->copy()->subMonth()->startOfMonth(),
                'end' => $now->copy()->subMonth()->endOfMonth(),
                'label' => 'Bulan Lalu ('.$now->copy()->subMonth()->startOfMonth()->format('d/m/Y').' s/d '.$now->copy()->subMonth()->endOfMonth()->format('d/m/Y').')',
                'is_all' => false,
            ],
            'last_3_months' => [
                'start' => $now->copy()->subMonths(2)->startOfMonth(),
                'end' => $now->copy()->endOfDay(),
                'label' => '3 Bulan Terakhir ('.$now->copy()->subMonths(2)->startOfMonth()->format('d/m/Y').' s/d '.$now->format('d/m/Y').')',
                'is_all' => false,
            ],
            'custom' => $this->parseCustomDateBounds($filters),
            default => [
                'start' => null,
                'end' => null,
                'label' => 'Semua Waktu',
                'is_all' => true,
            ],
        };
    }

    /**
     * Hitung rentang kustom dari input tanggal.
     *
     * @param  array<string, mixed>  $filters
     * @return array{start: ?Carbon, end: ?Carbon, label: string, is_all: bool}
     */
    protected function parseCustomDateBounds(array $filters): array
    {
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        if (blank($dateFrom) && blank($dateTo)) {
            return [
                'start' => null,
                'end' => null,
                'label' => 'Semua Waktu (Kustom Tanpa Batas)',
                'is_all' => true,
            ];
        }

        $start = ! blank($dateFrom) ? Carbon::parse($dateFrom, 'Asia/Jakarta')->startOfDay() : null;
        $end = ! blank($dateTo) ? Carbon::parse($dateTo, 'Asia/Jakarta')->endOfDay() : null;

        $label = 'Kustom (';
        $label .= $start ? $start->format('d/m/Y') : '-';
        $label .= ' s/d ';
        $label .= $end ? $end->format('d/m/Y') : '-';
        $label .= ')';

        return [
            'start' => $start,
            'end' => $end,
            'label' => $label,
            'is_all' => false,
        ];
    }

    /**
     * Format nama berkas sesuai spesifikasi:
     * janji-temu_{dari}_{sampai}_{ringkasan-status}.xlsx
     * Contoh: janji-temu_2026-10-01_2026-10-31_dibatalkan.xlsx
     * Untuk Semua waktu gunakan "semua-waktu"; untuk semua status gunakan "semua-status".
     *
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>|null  $dateBounds
     */
    public function generateFilename(array $filters, string $format, ?array $dateBounds): string
    {
        $format = strtolower($format) === 'csv' ? 'csv' : 'xlsx';

        // 1. Bagian Periode
        if (empty($dateBounds) || ($dateBounds['is_all'] ?? false) || empty($dateBounds['start']) || empty($dateBounds['end'])) {
            $periodPart = 'semua-waktu';
        } else {
            $fromStr = $dateBounds['start']->format('Y-m-d');
            $toStr = $dateBounds['end']->format('Y-m-d');
            $periodPart = "{$fromStr}_{$toStr}";
        }

        // 2. Bagian Ringkasan Status
        $allStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $selectedStatuses = (array) ($filters['statuses'] ?? []);

        $statusSlugMap = [
            'pending' => 'menunggu',
            'confirmed' => 'dikonfirmasi',
            'completed' => 'selesai',
            'cancelled' => 'dibatalkan',
            'no_show' => 'tidak-hadir',
        ];

        if (empty($selectedStatuses) || count(array_intersect($allStatuses, $selectedStatuses)) === count($allStatuses)) {
            $statusPart = 'semua-status';
        } else {
            $slugs = [];
            foreach ($selectedStatuses as $st) {
                if (isset($statusSlugMap[$st])) {
                    $slugs[] = $statusSlugMap[$st];
                }
            }
            $statusPart = ! empty($slugs) ? implode('-', $slugs) : 'semua-status';
        }

        return "janji-temu_{$periodPart}_{$statusPart}.{$format}";
    }

    /**
     * Proses ekspor dan kembalikan BinaryFileResponse untuk diunduh langsung.
     *
     * @param  array<string, mixed>  $filters
     */
    public function export(array $filters, string $format = 'xlsx'): BinaryFileResponse
    {
        $format = strtolower($format) === 'csv' ? 'csv' : 'xlsx';
        $dateBounds = $this->calculateDateBounds($filters);
        $query = $this->queryForFilters($filters);

        $tempDir = storage_path('app/temp-exports');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempFileName = Str::random(32).'.'.$format;
        $tempFilePath = $tempDir.DIRECTORY_SEPARATOR.$tempFileName;

        $exporter = new AppointmentExport($filters, $query, $format, $dateBounds);
        $totalRows = $exporter->exportToFile($tempFilePath);

        $filename = $this->generateFilename($filters, $format, $dateBounds);

        // Catat aktivitas ekspor ke log TANPA memuat data pasien
        Log::info('Janji temu diekspor oleh admin', [
            'admin_id' => auth()->id() ?? 'system',
            'admin_email' => auth()->user()?->email ?? 'unknown',
            'exported_at' => Carbon::now('Asia/Jakarta')->toIso8601String(),
            'period_by' => $filters['period_by'] ?? 'appointment_date',
            'date_range' => $filters['date_range'] ?? 'all',
            'date_from' => $dateBounds['start']?->format('Y-m-d'),
            'date_to' => $dateBounds['end']?->format('Y-m-d'),
            'statuses' => $filters['statuses'] ?? [],
            'branch_count' => count((array) ($filters['branch_ids'] ?? [])),
            'service_count' => count((array) ($filters['service_ids'] ?? [])),
            'include_complaints' => (bool) ($filters['include_complaints'] ?? false),
            'format' => $format,
            'row_count' => $totalRows,
        ]);

        $mimeType = $format === 'csv'
            ? 'text/csv; charset=UTF-8'
            : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

        return response()->download($tempFilePath, $filename, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'no-cache, private',
        ])->deleteFileAfterSend(true);
    }
}
