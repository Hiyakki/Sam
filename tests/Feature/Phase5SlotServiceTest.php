<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\BranchClosure;
use App\Models\BranchSchedule;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class Phase5SlotServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SlotService $slotService;

    protected Branch $branch;

    protected Service $serviceA;

    protected Service $serviceB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slotService = app(SlotService::class);

        SiteSetting::set('general.appointment_max_days', '60');
        SiteSetting::set('general.slot_buffer_minutes', '60');

        $this->branch = Branch::create([
            'name' => 'SAM Cabang Solo',
            'slug' => 'sam-solo',
            'address' => 'Jl. Veteran No. 12, Surakarta',
            'phone_wa' => '081234567890',
            'is_active' => true,
        ]);

        $this->serviceA = Service::create([
            'name' => 'Pemeriksaan Ortotik',
            'slug' => 'pemeriksaan-ortotik',
            'is_active' => true,
        ]);

        $this->serviceB = Service::create([
            'name' => 'Layanan Khusus Non-Cabang',
            'slug' => 'layanan-khusus',
            'is_active' => true,
        ]);

        // Hubungkan hanya serviceA ke branch
        $this->branch->services()->attach($this->serviceA->id);

        // Buat jadwal reguler: Senin - Sabtu buka (08:00 - 12:00, durasi 60m, kapasitas 1), Minggu tutup
        for ($day = 1; $day <= 7; $day++) {
            BranchSchedule::create([
                'branch_id' => $this->branch->id,
                'day_of_week' => $day,
                'is_open' => ($day !== 7), // Minggu tutup
                'open_time' => '08:00:00',
                'close_time' => '12:00:00',
                'slot_duration_minutes' => 60,
                'capacity_per_slot' => 1,
            ]);
        }
    }

    public function test_available_services_returns_only_active_services_attached_to_branch(): void
    {
        $services = $this->slotService->availableServices($this->branch);

        $this->assertCount(1, $services);
        $this->assertEquals($this->serviceA->id, $services->first()->id);
    }

    public function test_sunday_is_not_bookable(): void
    {
        // Cari hari Minggu mendatang
        $nextSunday = Carbon::now('Asia/Jakarta')->next(Carbon::SUNDAY);

        $this->assertFalse($this->slotService->isDateBookable($this->branch, $nextSunday));
        $this->assertEmpty($this->slotService->slotsFor($this->branch, $nextSunday));
    }

    public function test_branch_specific_closure_is_not_bookable(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::WEDNESDAY);

        BranchClosure::create([
            'branch_id' => $this->branch->id,
            'date' => $targetDate->format('Y-m-d'),
            'reason' => 'Renovasi Klinik',
            'is_closed' => true,
        ]);

        $this->assertFalse($this->slotService->isDateBookable($this->branch, $targetDate));
        $this->assertEmpty($this->slotService->slotsFor($this->branch, $targetDate));
    }

    public function test_global_all_branch_closure_is_not_bookable(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::THURSDAY);

        BranchClosure::create([
            'branch_id' => null, // Berlaku untuk semua cabang
            'date' => $targetDate->format('Y-m-d'),
            'reason' => 'Hari Libur Nasional',
            'is_closed' => true,
        ]);

        $this->assertFalse($this->slotService->isDateBookable($this->branch, $targetDate));
        $this->assertEmpty($this->slotService->slotsFor($this->branch, $targetDate));
    }

    public function test_custom_hours_closure_adjusts_available_slots(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::FRIDAY);

        BranchClosure::create([
            'branch_id' => $this->branch->id,
            'date' => $targetDate->format('Y-m-d'),
            'reason' => 'Jadwal Khusus Setengah Hari',
            'is_closed' => false,
            'open_time' => '08:00:00',
            'close_time' => '10:00:00',
        ]);

        $slots = $this->slotService->slotsFor($this->branch, $targetDate);

        // Dengan durasi 60 menit antara 08:00 sampai 10:00 ada tepat 2 slot: 08:00-09:00 dan 09:00-10:00
        $this->assertCount(2, $slots);
        $this->assertEquals('08:00', $slots[0]['start']);
        $this->assertEquals('09:00', $slots[0]['end']);
        $this->assertEquals('09:00', $slots[1]['start']);
        $this->assertEquals('10:00', $slots[1]['end']);
    }

    public function test_past_date_and_exceeding_max_days_are_not_bookable(): void
    {
        $yesterday = Carbon::yesterday('Asia/Jakarta');
        $this->assertFalse($this->slotService->isDateBookable($this->branch, $yesterday));

        $day61 = Carbon::today('Asia/Jakarta')->addDays(61);
        $this->assertFalse($this->slotService->isDateBookable($this->branch, $day61));
    }

    public function test_full_slot_is_marked_unavailable(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::TUESDAY);

        // Buat janji yang mengisi slot 08:00 (kapasitas 1)
        Appointment::create([
            'branch_id' => $this->branch->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => $targetDate->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'patient_name' => 'Pasien A',
            'patient_email' => 'pasien.a@example.com',
            'patient_phone' => '628111111111',
            'patient_age' => 30,
            'complaint' => 'Drop foot',
            'status' => 'confirmed',
        ]);

        $slots = $this->slotService->slotsFor($this->branch, $targetDate);

        $slot08 = collect($slots)->firstWhere('start', '08:00');
        $slot09 = collect($slots)->firstWhere('start', '09:00');

        $this->assertNotNull($slot08);
        $this->assertFalse($slot08['available']);
        $this->assertEquals(1, $slot08['booked_count']);
        $this->assertEquals('Slot penuh', $slot08['reason']);

        $this->assertNotNull($slot09);
        $this->assertTrue($slot09['available']);
        $this->assertEquals(0, $slot09['booked_count']);
    }

    public function test_past_slots_today_with_buffer_are_unavailable(): void
    {
        // Asumsikan waktu sekarang adalah hari Senin jam 09:30 WIB
        $today = Carbon::parse('2026-10-12 09:30:00', 'Asia/Jakarta'); // 2026-10-12 adalah hari Senin
        Carbon::setTestNow($today);

        $slots = $this->slotService->slotsFor($this->branch, $today);

        // Jadwal buka 08:00 - 12:00.
        // Buffer 60 menit: cutoff time adalah 09:30 + 60m = 10:30.
        // Maka:
        // Slot 08:00 <= 10:30 (Unavailable - Waktu telah lewat)
        // Slot 09:00 <= 10:30 (Unavailable - Waktu telah lewat)
        // Slot 10:00 <= 10:30 (Unavailable - Waktu telah lewat)
        // Slot 11:00 > 10:30 (Available)
        $slot08 = collect($slots)->firstWhere('start', '08:00');
        $slot09 = collect($slots)->firstWhere('start', '09:00');
        $slot10 = collect($slots)->firstWhere('start', '10:00');
        $slot11 = collect($slots)->firstWhere('start', '11:00');

        $this->assertFalse($slot08['available']);
        $this->assertFalse($slot09['available']);
        $this->assertFalse($slot10['available']);
        $this->assertTrue($slot11['available']);

        Carbon::setTestNow(); // Reset time
    }

    public function test_booking_service_not_attached_to_branch_is_rejected(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::MONDAY);

        $this->expectException(ValidationException::class);

        $this->slotService->book([
            'branch_id' => $this->branch->id,
            'service_id' => $this->serviceB->id, // serviceB tidak di-attach ke branch
            'appointment_date' => $targetDate->format('Y-m-d'),
            'start_time' => '09:00',
            'patient_name' => 'Budi Santoso',
            'patient_email' => 'budi@example.com',
            'patient_phone' => '081234567890',
            'patient_age' => 45,
            'complaint' => 'Konsultasi ortosis',
        ]);
    }

    public function test_booking_successful_and_creates_appointment_without_code(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::WEDNESDAY);

        $appointment = $this->slotService->book([
            'branch_id' => $this->branch->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => $targetDate->format('Y-m-d'),
            'start_time' => '09:00',
            'patient_name' => 'Siti Aminah',
            'patient_email' => 'siti@example.com',
            'patient_phone' => '081298765432',
            'patient_age' => 28,
            'complaint' => 'Kaki sering lemas pasca stroke',
        ]);

        $this->assertInstanceOf(Appointment::class, $appointment);
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
        $this->assertEquals('pending', $appointment->status);
        $this->assertEquals('6281298765432', $appointment->patient_phone); // Normalisasi nomor
    }

    public function test_concurrency_protection_double_booking_same_slot_fails(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::THURSDAY);

        // Booking pertama berhasil
        $appt1 = $this->slotService->book([
            'branch_id' => $this->branch->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => $targetDate->format('Y-m-d'),
            'start_time' => '10:00',
            'patient_name' => 'Pemesan Pertama',
            'patient_email' => 'pertama@example.com',
            'patient_phone' => '08111111111',
            'patient_age' => 35,
            'complaint' => 'Keluhan 1',
        ]);

        $this->assertNotNull($appt1);

        // Booking kedua pada slot yang sama persis (kapasitas 1) harus gagal melempar ValidationException
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Slot sudah terisi, silakan pilih jam lain.');

        $this->slotService->book([
            'branch_id' => $this->branch->id,
            'service_id' => $this->serviceA->id,
            'appointment_date' => $targetDate->format('Y-m-d'),
            'start_time' => '10:00',
            'patient_name' => 'Pemesan Kedua',
            'patient_email' => 'kedua@example.com',
            'patient_phone' => '08222222222',
            'patient_age' => 40,
            'complaint' => 'Keluhan 2',
        ]);
    }
}
