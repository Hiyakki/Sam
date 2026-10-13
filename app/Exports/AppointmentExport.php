<?php

namespace App\Exports;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\AutoFilter;
use OpenSpout\Writer\CSV\Writer as CsvWriter;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

class AppointmentExport
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        protected array $filters,
        protected Builder $query,
        protected string $format = 'xlsx',
        protected ?array $dateBounds = null
    ) {}

    /**
     * Tulis berkas ekspor ke path tujuan.
     */
    public function exportToFile(string $filePath): int
    {
        return $this->format === 'csv'
            ? $this->exportCsv($filePath)
            : $this->exportXlsx($filePath);
    }

    /**
     * Ekspor format XLSX dengan dua sheet (Janji Temu & Ringkasan).
     */
    protected function exportXlsx(string $filePath): int
    {
        $writer = new XlsxWriter;
        $writer->openToFile($filePath);

        $includeComplaints = (bool) ($this->filters['include_complaints'] ?? false);

        // --- SHEET 1: Janji Temu ---
        $sheet1 = $writer->getCurrentSheet();
        $sheet1->setName('Janji Temu');

        // Freeze pane baris pertama (header)
        $sheetView = (new SheetView)->setFreezeRow(2);
        $sheet1->setSheetView($sheetView);

        // Lebar kolom yang wajar
        $this->applyColumnWidths($sheet1, $includeComplaints);

        // Gaya header: tebal, background navy #1E2A4A, teks putih, wrap text
        $headerStyle = (new Style)
            ->setFontBold()
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('1E2A4A')
            ->setShouldWrapText();

        $headers = $this->buildHeaders($includeComplaints);
        $writer->addRow(Row::fromValues($headers, $headerStyle));

        $dataStyle = (new Style)->setShouldWrapText();
        $rowNumber = 0;

        // Streaming chunked tanpa N+1 (500 baris per chunk)
        (clone $this->query)->chunk(500, function ($appointments) use (&$rowNumber, $writer, $dataStyle, $includeComplaints) {
            foreach ($appointments as $appointment) {
                $rowNumber++;
                $rowValues = $this->mapAppointmentToRow($appointment, $rowNumber, $includeComplaints, 'xlsx');
                $writer->addRow(Row::fromValues($rowValues, $dataStyle));
            }
        });

        // Pasang AutoFilter pada Sheet 1
        $lastColIndex = count($headers) - 1;
        $sheet1->setAutoFilter(new AutoFilter(0, 1, $lastColIndex, max(1, $rowNumber + 1)));

        // --- SHEET 2: Ringkasan ---
        $sheet2 = $writer->addNewSheetAndMakeItCurrent();
        $sheet2->setName('Ringkasan');
        $sheet2->setColumnWidth(26, 1);
        $sheet2->setColumnWidth(40, 2);

        $this->writeSummarySheet($writer, $headerStyle, $dataStyle, $rowNumber);

        $writer->close();

        return $rowNumber;
    }

    /**
     * Ekspor format CSV (UTF-8 dengan BOM otomatis dari OpenSpout).
     */
    protected function exportCsv(string $filePath): int
    {
        $writer = new CsvWriter;
        $writer->openToFile($filePath);

        $includeComplaints = (bool) ($this->filters['include_complaints'] ?? false);
        $headers = $this->buildHeaders($includeComplaints);
        $writer->addRow(Row::fromValues($headers));

        $rowNumber = 0;
        (clone $this->query)->chunk(500, function ($appointments) use (&$rowNumber, $writer, $includeComplaints) {
            foreach ($appointments as $appointment) {
                $rowNumber++;
                $rowValues = $this->mapAppointmentToRow($appointment, $rowNumber, $includeComplaints, 'csv');
                $writer->addRow(Row::fromValues($rowValues));
            }
        });

        $writer->close();

        return $rowNumber;
    }

    /**
     * Daftar nama kolom untuk header.
     *
     * @return array<int, string>
     */
    protected function buildHeaders(bool $includeComplaints): array
    {
        $headers = [
            'No',
            'Tanggal Janji',
            'Jam',
            'Nama',
            'Telepon',
            'Email',
            'Usia',
            'Kantor',
            'Layanan',
            'Status',
        ];

        if ($includeComplaints) {
            $headers[] = 'Keluhan';
        }

        $headers[] = 'Catatan Admin';
        $headers[] = 'Resep';
        $headers[] = 'Dibuat Pada';

        return $headers;
    }

    /**
     * Atur lebar kolom untuk Sheet 1 (XLSX).
     */
    protected function applyColumnWidths($sheet, bool $includeComplaints): void
    {
        $col = 1;
        $sheet->setColumnWidth(6, $col++);  // No
        $sheet->setColumnWidth(15, $col++); // Tanggal Janji
        $sheet->setColumnWidth(16, $col++); // Jam
        $sheet->setColumnWidth(24, $col++); // Nama
        $sheet->setColumnWidth(18, $col++); // Telepon
        $sheet->setColumnWidth(26, $col++); // Email
        $sheet->setColumnWidth(8, $col++);  // Usia
        $sheet->setColumnWidth(22, $col++); // Kantor
        $sheet->setColumnWidth(24, $col++); // Layanan
        $sheet->setColumnWidth(16, $col++); // Status

        if ($includeComplaints) {
            $sheet->setColumnWidth(35, $col++); // Keluhan
        }

        $sheet->setColumnWidth(30, $col++); // Catatan Admin
        $sheet->setColumnWidth(10, $col++); // Resep
        $sheet->setColumnWidth(18, $col++); // Dibuat Pada
    }

    /**
     * Transform model Appointment ke array nilai baris.
     *
     * @return array<int, mixed>
     */
    protected function mapAppointmentToRow(Appointment $appointment, int $rowNumber, bool $includeComplaints, string $format): array
    {
        $dateFormatted = $appointment->appointment_date ? $appointment->appointment_date->format('d/m/Y') : '-';

        $startTime = $appointment->start_time ? substr((string) $appointment->start_time, 0, 5) : '-';
        $endTime = $appointment->end_time ? substr((string) $appointment->end_time, 0, 5) : '-';
        $timeRange = ($startTime !== '-' && $endTime !== '-') ? "{$startTime}-{$endTime}" : $startTime;

        $phone = self::formatPhone((string) $appointment->patient_phone, $format);
        $hasPrescription = ! empty($appointment->prescription_path) ? 'Ada' : 'Tidak';
        $createdAt = $appointment->created_at ? $appointment->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-';

        $row = [
            $rowNumber,
            $dateFormatted,
            $timeRange,
            self::sanitizeFormula($appointment->patient_name),
            $phone,
            self::sanitizeFormula($appointment->patient_email),
            $appointment->patient_age ?? '-',
            self::sanitizeFormula($appointment->branch?->name ?? '-'),
            self::sanitizeFormula($appointment->service?->name ?? '-'),
            self::statusLabel((string) $appointment->status),
        ];

        if ($includeComplaints) {
            $row[] = self::sanitizeFormula($appointment->complaint);
        }

        $row[] = self::sanitizeFormula($appointment->admin_notes);
        $row[] = $hasPrescription;
        $row[] = $createdAt;

        return $row;
    }

    /**
     * Tulis lembar Ringkasan (Sheet 2).
     */
    protected function writeSummarySheet(XlsxWriter $writer, Style $headerStyle, Style $dataStyle, int $totalRows): void
    {
        $sectionStyle = (new Style)
            ->setFontBold()
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('374151') // Gray 700
            ->setShouldWrapText();

        $subHeaderStyle = (new Style)
            ->setFontBold()
            ->setBackgroundColor('F3F4F6'); // Gray 100

        // 1. Informasi Filter & Waktu Ekspor
        $writer->addRow(Row::fromValues(['RINGKASAN EKSPOR JANJI TEMU', ''], $headerStyle));

        $periodType = ($this->filters['period_by'] ?? 'appointment_date') === 'created_at'
            ? 'Tanggal Dibuat'
            : 'Tanggal Janji';

        $rangeLabel = $this->dateBounds['label'] ?? 'Semua Waktu';
        $exportTime = Carbon::now('Asia/Jakarta')->translatedFormat('d/m/Y H:i:s').' WIB';

        $selectedStatuses = array_map(
            fn ($s) => self::statusLabel((string) $s),
            (array) ($this->filters['statuses'] ?? [])
        );
        $statusText = empty($selectedStatuses) ? 'Semua Status' : implode(', ', $selectedStatuses);

        $branchText = ! empty($this->filters['branch_ids'])
            ? Branch::whereIn('id', $this->filters['branch_ids'])->pluck('name')->implode(', ')
            : 'Semua Kantor';

        $serviceText = ! empty($this->filters['service_ids'])
            ? Service::whereIn('id', $this->filters['service_ids'])->pluck('name')->implode(', ')
            : 'Semua Layanan';

        $complaintOption = ! empty($this->filters['include_complaints']) ? 'Disertakan' : 'Tidak Disertakan';

        $metadataRows = [
            ['Parameter', 'Nilai'],
            ['Jenis Periode', $periodType],
            ['Rentang Waktu', $rangeLabel],
            ['Filter Status', $statusText],
            ['Filter Kantor Cabang', $branchText],
            ['Filter Layanan', $serviceText],
            ['Opsi Keluhan Pasien', $complaintOption],
            ['Waktu Ekspor (Asia/Jakarta)', $exportTime],
            ['Total Janji Temu Diekspor', $totalRows],
        ];

        foreach ($metadataRows as $index => $meta) {
            $style = $index === 0 ? $subHeaderStyle : $dataStyle;
            $writer->addRow(Row::fromValues($meta, $style));
        }

        // Spasi kosong
        $writer->addRow(Row::fromValues(['', '']));

        // 2. Agregat per Status
        $writer->addRow(Row::fromValues(['JUMLAH PER STATUS', 'JUMLAH'], $sectionStyle));
        $statusCounts = (clone $this->query)
            ->reorder()
            ->groupBy('status')
            ->selectRaw('status, count(*) as aggregate_total')
            ->pluck('aggregate_total', 'status');

        $allStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        foreach ($allStatuses as $st) {
            $label = self::statusLabel($st);
            $count = $statusCounts[$st] ?? 0;
            $writer->addRow(Row::fromValues([$label, (int) $count], $dataStyle));
        }

        // Spasi kosong
        $writer->addRow(Row::fromValues(['', '']));

        // 3. Agregat per Layanan
        $writer->addRow(Row::fromValues(['JUMLAH PER LAYANAN', 'JUMLAH'], $sectionStyle));
        $serviceIdCounts = (clone $this->query)
            ->reorder()
            ->groupBy('service_id')
            ->selectRaw('service_id, count(*) as aggregate_total')
            ->pluck('aggregate_total', 'service_id');

        if ($serviceIdCounts->isEmpty()) {
            $writer->addRow(Row::fromValues(['(Tidak ada data layanan)', 0], $dataStyle));
        } else {
            $servicesMap = Service::whereIn('id', $serviceIdCounts->keys())->pluck('name', 'id');
            foreach ($serviceIdCounts as $sId => $count) {
                $sName = $servicesMap[$sId] ?? "Layanan #{$sId}";
                $writer->addRow(Row::fromValues([self::sanitizeFormula($sName), (int) $count], $dataStyle));
            }
        }

        // Spasi kosong
        $writer->addRow(Row::fromValues(['', '']));

        // 4. Agregat per Kantor Cabang
        $writer->addRow(Row::fromValues(['JUMLAH PER KANTOR', 'JUMLAH'], $sectionStyle));
        $branchIdCounts = (clone $this->query)
            ->reorder()
            ->groupBy('branch_id')
            ->selectRaw('branch_id, count(*) as aggregate_total')
            ->pluck('aggregate_total', 'branch_id');

        if ($branchIdCounts->isEmpty()) {
            $writer->addRow(Row::fromValues(['(Tidak ada data kantor)', 0], $dataStyle));
        } else {
            $branchesMap = Branch::whereIn('id', $branchIdCounts->keys())->pluck('name', 'id');
            foreach ($branchIdCounts as $bId => $count) {
                $bName = $branchesMap[$bId] ?? "Kantor #{$bId}";
                $writer->addRow(Row::fromValues([self::sanitizeFormula($bName), (int) $count], $dataStyle));
            }
        }
    }

    /**
     * Netralkan formula injection: karakter diawali =, +, -, @, \t, \r dinetralkan dengan apostrof.
     */
    public static function sanitizeFormula(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $injectionChars = ['=', '+', '-', '@', "\t", "\r"];

        if (in_array($value[0], $injectionChars, true)) {
            return "'".$value;
        }

        $trimmed = ltrim($value);
        if ($trimmed !== '' && in_array($trimmed[0], $injectionChars, true)) {
            return "'".$value;
        }

        return $value;
    }

    /**
     * Pastikan nomor telepon disimpan sebagai teks agar 0 atau 62 di depan tidak hilang.
     */
    public static function formatPhone(?string $phone, string $format): string
    {
        if ($phone === null || $phone === '') {
            return '-';
        }

        $phoneStr = (string) $phone;

        // Jika nomor telepon diawali formula char seperti +, sanitizeFormula akan menangani
        if (str_starts_with($phoneStr, '+')) {
            return "'".$phoneStr;
        }

        // Pada format CSV, pastikan diawali tanda petik agar Excel tidak mengubahnya jadi angka ilmiah
        if ($format === 'csv' && ! str_starts_with($phoneStr, "'")) {
            return "'".$phoneStr;
        }

        return $phoneStr;
    }

    /**
     * Label status Janji Temu dalam Bahasa Indonesia.
     */
    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'no_show' => 'Tidak Hadir',
            default => ucfirst($status),
        };
    }
}
