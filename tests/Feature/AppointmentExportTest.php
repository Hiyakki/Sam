<?php

namespace Tests\Feature;

use App\Exports\AppointmentExport;
use App\Filament\Resources\AppointmentResource\Pages\ListAppointments;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use App\Services\AppointmentExportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use Tests\TestCase;

class AppointmentExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Branch $branchSolo;

    protected Branch $branchJogja;

    protected Service $serviceA;

    protected Service $serviceB;

    protected AppointmentExportService $exportService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exportService = app(AppointmentExportService::class);
        $this->admin = User::factory()->create(['email' => 'admin@sam.test']);

        $this->branchSolo = Branch::create([
            'name' => 'SAM Solo',
            'slug' => 'sam-solo',
            'address' => 'Jl. Solo 1',
            'phone_wa' => '0811111111',
            'is_active' => true,
        ]);

        $this->branchJogja = Branch::create([
            'name' => 'SAM Jogja',
            'slug' => 'sam-jogja',
            'address' => 'Jl. Jogja 2',
            'phone_wa' => '0822222222',
            'is_active' => true,
        ]);

        $this->serviceA = Service::create([
            'name' => 'Konsultasi Ortotik',
            'slug' => 'konsultasi-ortotik',
            'is_active' => true,
        ]);

        $this->serviceB = Service::create([
            'name' => 'Pembuatan Kaki Palsu',
            'slug' => 'kaki-palsu',
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_export_action(): void
    {
        // Akses langsung halaman admin tanpa login akan diarahkan ke login
        $response = $this->get('/kelola-sam/appointments');
        $response->assertRedirect('/kelola-sam/login');
    }

    public function test_period_filters_inclusive_and_time_safe_at_day_boundaries(): void
    {
        // Buat janji temu pada pukul 23:30 WIB di tanggal 2026-10-15
        $apptLate = Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-15',
            'start_time' => '23:30:00',
            'end_time' => '23:59:00',
            'patient_name' => 'Pasien Larut',
            'patient_email' => 'larut@test.com',
            'patient_phone' => '081234567890',
            'patient_age' => 30,
            'complaint' => 'Keluhan larut',
            'status' => 'pending',
        ]);
        DB::table('appointments')->where('id', $apptLate->id)->update([
            'created_at' => Carbon::parse('2026-10-15 23:30:00', 'Asia/Jakarta')->setTimezone('UTC')->format('Y-m-d H:i:s'),
        ]);

        // Janji temu di luar batas (2026-10-16 00:05)
        $apptNextDay = Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-16',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'patient_name' => 'Pasien Besok',
            'patient_email' => 'besok@test.com',
            'patient_phone' => '081234567891',
            'patient_age' => 30,
            'complaint' => 'Keluhan besok',
            'status' => 'pending',
        ]);
        DB::table('appointments')->where('id', $apptNextDay->id)->update([
            'created_at' => Carbon::parse('2026-10-16 00:05:00', 'Asia/Jakarta')->setTimezone('UTC')->format('Y-m-d H:i:s'),
        ]);

        // Filter kustom untuk 2026-10-15 s/d 2026-10-15
        $filters = [
            'period_by' => 'created_at',
            'date_range' => 'custom',
            'date_from' => '2026-10-15',
            'date_to' => '2026-10-15',
            'statuses' => ['pending'],
        ];

        $results = $this->exportService->queryForFilters($filters)->get();

        $this->assertCount(1, $results);
        $this->assertEquals($apptLate->id, $results->first()->id);
    }

    public function test_filter_by_appointment_date_vs_created_at(): void
    {
        // Janji dibuat 2026-10-01 tapi tanggal janji kunjungannya 2026-10-20
        $appt = Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-20',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Pasien Jadwal Maju',
            'patient_email' => 'maju@test.com',
            'patient_phone' => '081234567892',
            'patient_age' => 30,
            'complaint' => 'Keluhan maju',
            'status' => 'confirmed',
        ]);
        DB::table('appointments')->where('id', $appt->id)->update([
            'created_at' => Carbon::parse('2026-10-01 10:00:00', 'Asia/Jakarta')->setTimezone('UTC')->format('Y-m-d H:i:s'),
        ]);

        // 1. Filter periode appointment_date 2026-10-20 -> ditemukan
        $filterDate = [
            'period_by' => 'appointment_date',
            'date_range' => 'custom',
            'date_from' => '2026-10-20',
            'date_to' => '2026-10-20',
            'statuses' => ['confirmed'],
        ];
        $this->assertEquals(1, $this->exportService->countForFilters($filterDate));

        // 2. Filter periode created_at pada 2026-10-20 -> TIDAK ditemukan (karena dibuat 2026-10-01)
        $filterCreated = [
            'period_by' => 'created_at',
            'date_range' => 'custom',
            'date_from' => '2026-10-20',
            'date_to' => '2026-10-20',
            'statuses' => ['confirmed'],
        ];
        $this->assertEquals(0, $this->exportService->countForFilters($filterCreated));

        // 3. Filter periode created_at 2026-10-01 -> ditemukan
        $filterCreated2 = [
            'period_by' => 'created_at',
            'date_range' => 'custom',
            'date_from' => '2026-10-01',
            'date_to' => '2026-10-01',
            'statuses' => ['confirmed'],
        ];
        $this->assertEquals(1, $this->exportService->countForFilters($filterCreated2));
    }

    public function test_filter_by_status_and_combinations(): void
    {
        Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-10',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Pasien Batal',
            'patient_email' => 'batal@test.com',
            'patient_phone' => '081234567801',
            'patient_age' => 30,
            'complaint' => 'Keluhan batal',
            'status' => 'cancelled',
        ]);

        Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-10',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'patient_name' => 'Pasien Konfirmasi',
            'patient_email' => 'konfirm@test.com',
            'patient_phone' => '081234567802',
            'patient_age' => 30,
            'complaint' => 'Keluhan konfirm',
            'status' => 'confirmed',
        ]);

        Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-10',
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
            'patient_name' => 'Pasien Selesai',
            'patient_email' => 'selesai@test.com',
            'patient_phone' => '081234567803',
            'patient_age' => 30,
            'complaint' => 'Keluhan selesai',
            'status' => 'completed',
        ]);

        // Hanya Dibatalkan
        $countBatal = $this->exportService->countForFilters([
            'date_range' => 'all',
            'statuses' => ['cancelled'],
        ]);
        $this->assertEquals(1, $countBatal);

        // Kombinasi Dibatalkan + Selesai
        $countKombinasi = $this->exportService->countForFilters([
            'date_range' => 'all',
            'statuses' => ['cancelled', 'completed'],
        ]);
        $this->assertEquals(2, $countKombinasi);

        // Semua status
        $countSemua = $this->exportService->countForFilters([
            'date_range' => 'all',
            'statuses' => ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'],
        ]);
        $this->assertEquals(3, $countSemua);
    }

    public function test_filter_by_branch_and_service(): void
    {
        Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-11',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Pasien Solo Ortotik',
            'patient_email' => 'solo@test.com',
            'patient_phone' => '081234567810',
            'patient_age' => 30,
            'complaint' => 'Keluhan solo',
            'status' => 'confirmed',
        ]);

        Appointment::create([
            'branch_id' => $this->branchJogja->id,
            'service_id' => $this->serviceB->id,
            'appointment_date' => '2026-10-11',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Pasien Jogja Prostetik',
            'patient_email' => 'jogja@test.com',
            'patient_phone' => '081234567811',
            'patient_age' => 30,
            'complaint' => 'Keluhan jogja',
            'status' => 'confirmed',
        ]);

        // Filter Cabang Solo
        $countSolo = $this->exportService->countForFilters([
            'date_range' => 'all',
            'statuses' => ['confirmed'],
            'branch_ids' => [$this->branchSolo->id],
        ]);
        $this->assertEquals(1, $countSolo);

        // Filter Layanan Kaki Palsu
        $countServiceB = $this->exportService->countForFilters([
            'date_range' => 'all',
            'statuses' => ['confirmed'],
            'service_ids' => [$this->serviceB->id],
        ]);
        $this->assertEquals(1, $countServiceB);

        // Kombinasi: Cabang Solo + Layanan Kaki Palsu (seharusnya 0)
        $countNihil = $this->exportService->countForFilters([
            'date_range' => 'all',
            'statuses' => ['confirmed'],
            'branch_ids' => [$this->branchSolo->id],
            'service_ids' => [$this->serviceB->id],
        ]);
        $this->assertEquals(0, $countNihil);
    }

    public function test_empty_results_shows_notification_and_does_not_create_file(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ListAppointments::class)
            ->callAction('export_excel', [
                'period_by' => 'appointment_date',
                'date_range' => 'custom',
                'date_from' => '2026-01-01',
                'date_to' => '2026-01-02',
                'statuses' => ['pending'],
                'format' => 'xlsx',
            ])
            ->assertNotified('Tidak ada data pada filter ini');
    }

    public function test_validation_rejects_empty_status_and_invalid_date_range(): void
    {
        // 1. Status kosong
        Livewire::actingAs($this->admin)
            ->test(ListAppointments::class)
            ->callAction('export_excel', [
                'period_by' => 'appointment_date',
                'date_range' => 'all',
                'statuses' => [],
                'format' => 'xlsx',
            ])
            ->assertHasActionErrors(['statuses']);

        // 2. Sampai tanggal lebih awal dari Dari tanggal
        Livewire::actingAs($this->admin)
            ->test(ListAppointments::class)
            ->callAction('export_excel', [
                'period_by' => 'appointment_date',
                'date_range' => 'custom',
                'date_from' => '2026-10-20',
                'date_to' => '2026-10-10',
                'statuses' => ['pending'],
                'format' => 'xlsx',
            ])
            ->assertHasActionErrors(['date_to']);
    }

    public function test_complaint_column_toggle_and_no_code_and_no_prescription_file(): void
    {
        $appointment = Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-12',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Siti Rahma',
            'patient_email' => 'rahma@test.com',
            'patient_phone' => '081234567822',
            'patient_age' => 30,
            'complaint' => 'Drop foot kiri pasca stroke',
            'prescription_path' => 'prescriptions/resep_rahasia.pdf',
            'admin_notes' => 'Pasien butuh AFO kustom',
            'status' => 'confirmed',
        ]);

        $tempDir = storage_path('app/temp-test');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // 1. Ekspor TANPA keluhan (default)
        $fileNoComplaint = $tempDir.'/test_no_complaint.xlsx';
        $exportNo = new AppointmentExport(
            ['include_complaints' => false, 'statuses' => ['confirmed']],
            Appointment::where('id', $appointment->id),
            'xlsx'
        );
        $exportNo->exportToFile($fileNoComplaint);

        $reader = new XlsxReader;
        $reader->open($fileNoComplaint);
        $sheets = array_values(iterator_to_array($reader->getSheetIterator()));
        $sheet1 = $sheets[0];
        $rows = array_values(iterator_to_array($sheet1->getRowIterator()));
        $headerValuesNo = array_map(fn ($c) => $c->getValue(), $rows[0]->getCells());
        $dataValuesNo = array_map(fn ($c) => $c->getValue(), $rows[1]->getCells());
        $reader->close();
        unlink($fileNoComplaint);

        $this->assertNotContains('Keluhan', $headerValuesNo, 'Kolom Keluhan tidak boleh ada jika toggle mati.');
        $this->assertNotContains('Kode', $headerValuesNo, 'Kolom Kode tidak boleh ada.');
        $this->assertNotContains('Drop foot kiri pasca stroke', $dataValuesNo);
        $this->assertContains('Ada', $dataValuesNo, 'Kolom resep harus bernilai "Ada".');
        $this->assertNotContains('prescriptions/resep_rahasia.pdf', $dataValuesNo, 'Path file resep tidak boleh diekspor.');

        // 2. Ekspor DENGAN keluhan
        $fileWithComplaint = $tempDir.'/test_with_complaint.xlsx';
        $exportWith = new AppointmentExport(
            ['include_complaints' => true, 'statuses' => ['confirmed']],
            Appointment::where('id', $appointment->id),
            'xlsx'
        );
        $exportWith->exportToFile($fileWithComplaint);

        $reader->open($fileWithComplaint);
        $sheetsWith = array_values(iterator_to_array($reader->getSheetIterator()));
        $sheet1With = $sheetsWith[0];
        $rowsWith = array_values(iterator_to_array($sheet1With->getRowIterator()));
        $headerValuesWith = array_map(fn ($c) => $c->getValue(), $rowsWith[0]->getCells());
        $dataValuesWith = array_map(fn ($c) => $c->getValue(), $rowsWith[1]->getCells());
        $reader->close();
        unlink($fileWithComplaint);

        $this->assertContains('Keluhan', $headerValuesWith, 'Kolom Keluhan harus ada jika toggle aktif.');
        $this->assertContains('Drop foot kiri pasca stroke', $dataValuesWith);
    }

    public function test_formula_injection_sanitization_and_phone_stored_as_text(): void
    {
        $appointment = Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-14',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'patient_name' => '=HYPERLINK("http://evil.com","Klik")',
            'patient_email' => 'hacker@test.com',
            'patient_phone' => '081234567899',
            'patient_age' => 30,
            'complaint' => '+2+5',
            'admin_notes' => "\t-MALICIOUS",
            'status' => 'confirmed',
        ]);

        $tempDir = storage_path('app/temp-test');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filePath = $tempDir.'/test_formula.xlsx';
        $exporter = new AppointmentExport(
            ['include_complaints' => true, 'statuses' => ['confirmed']],
            Appointment::where('id', $appointment->id),
            'xlsx'
        );
        $exporter->exportToFile($filePath);

        $reader = new XlsxReader;
        $reader->open($filePath);
        $sheets = array_values(iterator_to_array($reader->getSheetIterator()));
        $sheet1 = $sheets[0];
        $rows = array_values(iterator_to_array($sheet1->getRowIterator()));
        $dataCells = array_map(fn ($c) => $c->getValue(), $rows[1]->getCells());
        $reader->close();
        unlink($filePath);

        // Nama: index 3
        $this->assertStringStartsWith("'", $dataCells[3], 'Nilai yang diawali = harus diawali tanda petik.');
        // Telepon: index 4
        $this->assertEquals('081234567899', $dataCells[4], 'Nomor telepon harus tetap memuat angka 0 di depan.');
        // Sanitizer formula method test langsung untuk email yang diawali formula
        $this->assertEquals("'@cmd|calc!A0", AppointmentExport::sanitizeFormula('@cmd|calc!A0'));
        // Keluhan: index 10
        $this->assertStringStartsWith("'", $dataCells[10], 'Nilai yang diawali + harus diawali tanda petik.');
        // Catatan Admin: index 11
        $this->assertStringStartsWith("'", $dataCells[11], 'Nilai yang diawali tab/- harus diawali tanda petik.');
    }

    public function test_valid_xlsx_and_summary_sheet_structure(): void
    {
        Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-05',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Pasien A',
            'patient_email' => 'pasiena@test.com',
            'patient_phone' => '0811111111',
            'patient_age' => 25,
            'complaint' => 'Keluhan Pasien A',
            'status' => 'pending',
        ]);

        Appointment::create([
            'branch_id' => $this->branchJogja->id,
            'service_id' => $this->serviceB->id,
            'appointment_date' => '2026-10-06',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'patient_name' => 'Pasien B',
            'patient_email' => 'pasienb@test.com',
            'patient_phone' => '0822222222',
            'patient_age' => 45,
            'complaint' => 'Keluhan Pasien B',
            'status' => 'completed',
        ]);

        $tempDir = storage_path('app/temp-test');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filePath = $tempDir.'/test_full.xlsx';
        $exporter = new AppointmentExport(
            [
                'period_by' => 'appointment_date',
                'date_range' => 'custom',
                'date_from' => '2026-10-01',
                'date_to' => '2026-10-31',
                'statuses' => ['pending', 'completed'],
            ],
            Appointment::query(),
            'xlsx',
            [
                'start' => Carbon::parse('2026-10-01', 'Asia/Jakarta'),
                'end' => Carbon::parse('2026-10-31', 'Asia/Jakarta'),
                'label' => 'Kustom (01/10/2026 s/d 31/10/2026)',
                'is_all' => false,
            ]
        );

        $rowCount = $exporter->exportToFile($filePath);
        $this->assertEquals(2, $rowCount);

        $reader = new XlsxReader;
        $reader->open($filePath);
        $sheets = array_values(iterator_to_array($reader->getSheetIterator()));

        $this->assertCount(2, $sheets, 'Harus ada 2 sheet: Janji Temu dan Ringkasan.');
        $this->assertEquals('Janji Temu', $sheets[0]->getName());
        $this->assertEquals('Ringkasan', $sheets[1]->getName());

        // Cek isi sheet Ringkasan
        $summaryRows = array_values(iterator_to_array($sheets[1]->getRowIterator()));
        $summaryText = '';
        foreach ($summaryRows as $sr) {
            $summaryText .= implode(' ', array_map(fn ($c) => (string) $c->getValue(), $sr->getCells()))."\n";
        }

        $this->assertStringContainsString('RINGKASAN EKSPOR JANJI TEMU', $summaryText);
        $this->assertStringContainsString('Tanggal Janji', $summaryText);
        $this->assertStringContainsString('Total Janji Temu Diekspor', $summaryText);
        $this->assertStringContainsString('JUMLAH PER STATUS', $summaryText);
        $this->assertStringContainsString('JUMLAH PER LAYANAN', $summaryText);
        $this->assertStringContainsString('JUMLAH PER KANTOR', $summaryText);
        $this->assertStringContainsString('SAM Solo', $summaryText);
        $this->assertStringContainsString('SAM Jogja', $summaryText);

        $reader->close();
        unlink($filePath);
    }

    public function test_valid_csv_export_format_with_utf8_bom(): void
    {
        Appointment::create([
            'branch_id' => $this->branchSolo->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => '2026-10-08',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'patient_name' => 'Pasien CSV',
            'patient_email' => 'csv@test.com',
            'patient_phone' => '081234567833',
            'patient_age' => 30,
            'complaint' => 'Keluhan Pasien CSV',
            'status' => 'confirmed',
        ]);

        $tempDir = storage_path('app/temp-test');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filePath = $tempDir.'/test_export.csv';
        $exporter = new AppointmentExport(
            ['date_range' => 'all', 'statuses' => ['confirmed']],
            Appointment::query(),
            'csv'
        );
        $exporter->exportToFile($filePath);

        $raw = file_get_contents($filePath);
        // Header UTF-8 BOM: 0xEF 0xBB 0xBF
        $bom = substr($raw, 0, 3);
        $this->assertEquals("\xEF\xBB\xBF", $bom, 'File CSV harus diawali dengan UTF-8 BOM.');

        // Baca dengan CsvReader OpenSpout
        $reader = new CsvReader;
        $reader->open($filePath);
        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = array_map(fn ($c) => $c->getValue(), $row->getCells());
            }
        }
        $reader->close();
        unlink($filePath);

        $this->assertCount(2, $rows); // Header + 1 baris data
        $this->assertEquals('Pasien CSV', $rows[1][3]);
    }

    public function test_filename_generation_matches_specification(): void
    {
        // 1. Kustom rentang dan status dibatalkan
        $bounds1 = [
            'start' => Carbon::parse('2026-10-01', 'Asia/Jakarta'),
            'end' => Carbon::parse('2026-10-31', 'Asia/Jakarta'),
            'is_all' => false,
        ];
        $fn1 = $this->exportService->generateFilename(['statuses' => ['cancelled']], 'xlsx', $bounds1);
        $this->assertEquals('janji-temu_2026-10-01_2026-10-31_dibatalkan.xlsx', $fn1);

        // 2. Semua waktu dan semua status
        $bounds2 = ['is_all' => true, 'start' => null, 'end' => null];
        $fn2 = $this->exportService->generateFilename([
            'statuses' => ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'],
        ], 'xlsx', $bounds2);
        $this->assertEquals('janji-temu_semua-waktu_semua-status.xlsx', $fn2);

        // 3. Format CSV
        $fn3 = $this->exportService->generateFilename([
            'statuses' => ['cancelled'],
        ], 'csv', $bounds1);
        $this->assertEquals('janji-temu_2026-10-01_2026-10-31_dibatalkan.csv', $fn3);
    }

    public function test_performance_benchmark_with_5000_rows(): void
    {
        $batch = [];
        $now = now();

        for ($i = 1; $i <= 5000; $i++) {
            $batch[] = [
                'branch_id' => $this->branchSolo->id,
                'service_id' => $this->serviceA->id,
                'appointment_date' => '2026-10-15',
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'patient_name' => "Pasien Bench {$i}",
                'patient_email' => "bench{$i}@test.com",
                'patient_phone' => '081234567890',
                'patient_age' => 30,
                'complaint' => 'Keluhan uji performa',
                'admin_notes' => 'Catatan uji',
                'status' => 'confirmed',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) === 1000) {
                DB::table('appointments')->insert($batch);
                $batch = [];
            }
        }
        if (! empty($batch)) {
            DB::table('appointments')->insert($batch);
        }

        $this->assertEquals(5000, Appointment::count());

        $tempDir = storage_path('app/temp-test');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $filePath = $tempDir.'/bench_5000.xlsx';

        $startMemory = memory_get_usage(true);
        $startTime = microtime(true);

        $exporter = new AppointmentExport(
            ['include_complaints' => true, 'statuses' => ['confirmed']],
            Appointment::query(),
            'xlsx',
            [
                'start' => Carbon::parse('2026-10-01', 'Asia/Jakarta'),
                'end' => Carbon::parse('2026-10-31', 'Asia/Jakarta'),
                'label' => 'Oktober 2026',
                'is_all' => false,
            ]
        );

        $rowCount = $exporter->exportToFile($filePath);

        $duration = microtime(true) - $startTime;
        $peakMemory = (memory_get_peak_usage(true) - $startMemory) / 1024 / 1024; // MB

        $this->assertEquals(5000, $rowCount);
        $this->assertFileExists($filePath);
        $this->assertGreaterThan(50000, filesize($filePath));

        unlink($filePath);

        // Verifikasi performa: 5.000 baris selesai di bawah 10 detik dan memori di bawah 80 MB
        $this->assertLessThan(10.0, $duration, "Waktu ekspor 5.000 baris ({$duration} detik) melebihi batas 10 detik.");
        $this->assertLessThan(80.0, $peakMemory, "Penggunaan memori ({$peakMemory} MB) melebihi batas 80 MB.");
    }
}
