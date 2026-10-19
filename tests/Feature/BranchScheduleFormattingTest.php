<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\BranchSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchScheduleFormattingTest extends TestCase
{
    use RefreshDatabase;

    public function test_grouped_schedules_groups_consecutive_days_with_identical_hours(): void
    {
        $branch = Branch::create([
            'name' => 'SAM Cabang Uji',
            'slug' => 'sam-cabang-uji',
            'address' => 'Jl. Uji No. 123',
            'phone_wa' => '081234567890',
            'is_active' => true,
        ]);

        // Senin - Sabtu: 08:00 - 17:00
        for ($i = 1; $i <= 6; $i++) {
            BranchSchedule::create([
                'branch_id' => $branch->id,
                'day_of_week' => $i,
                'is_open' => true,
                'open_time' => '08:00:00',
                'close_time' => '17:00:00',
                'slot_duration_minutes' => 60,
                'capacity_per_slot' => 1,
            ]);
        }

        // Minggu: Tutup
        BranchSchedule::create([
            'branch_id' => $branch->id,
            'day_of_week' => 7,
            'is_open' => false,
            'open_time' => null,
            'close_time' => null,
            'slot_duration_minutes' => 60,
            'capacity_per_slot' => 1,
        ]);

        $branch->load('schedules');
        $groups = $branch->grouped_schedules;

        $this->assertCount(2, $groups);

        // Group 1: Senin - Sabtu
        $this->assertEquals('Senin – Sabtu', $groups[0]['label']);
        $this->assertTrue($groups[0]['is_open']);
        $this->assertEquals('08:00 – 17:00 WIB', $groups[0]['hours']);
        $this->assertEquals([1, 2, 3, 4, 5, 6], $groups[0]['days']);

        // Group 2: Minggu
        $this->assertEquals('Minggu', $groups[1]['label']);
        $this->assertFalse($groups[1]['is_open']);
        $this->assertEquals('Tutup', $groups[1]['hours']);
        $this->assertEquals([7], $groups[1]['days']);
    }

    public function test_grouped_schedules_handles_different_operating_hours_cleanly(): void
    {
        $branch = Branch::create([
            'name' => 'SAM Khusus',
            'slug' => 'sam-khusus',
            'address' => 'Jl. Medis No. 45',
            'phone_wa' => '081234567891',
            'is_active' => true,
        ]);

        // Senin - Kamis: 08:00 - 17:00
        for ($i = 1; $i <= 4; $i++) {
            BranchSchedule::create([
                'branch_id' => $branch->id,
                'day_of_week' => $i,
                'is_open' => true,
                'open_time' => '08:00:00',
                'close_time' => '17:00:00',
            ]);
        }

        // Jumat: 08:00 - 16:30
        BranchSchedule::create([
            'branch_id' => $branch->id,
            'day_of_week' => 5,
            'is_open' => true,
            'open_time' => '08:00:00',
            'close_time' => '16:30:00',
        ]);

        // Sabtu: 08:00 - 14:00
        BranchSchedule::create([
            'branch_id' => $branch->id,
            'day_of_week' => 6,
            'is_open' => true,
            'open_time' => '08:00:00',
            'close_time' => '14:00:00',
        ]);

        // Minggu: Tutup
        BranchSchedule::create([
            'branch_id' => $branch->id,
            'day_of_week' => 7,
            'is_open' => false,
        ]);

        $branch->load('schedules');
        $groups = $branch->grouped_schedules;

        $this->assertCount(4, $groups);
        $this->assertEquals('Senin – Kamis', $groups[0]['label']);
        $this->assertEquals('08:00 – 17:00 WIB', $groups[0]['hours']);
        $this->assertEquals('Jumat', $groups[1]['label']);
        $this->assertEquals('08:00 – 16:30 WIB', $groups[1]['hours']);
        $this->assertEquals('Sabtu', $groups[2]['label']);
        $this->assertEquals('08:00 – 14:00 WIB', $groups[2]['hours']);
        $this->assertEquals('Minggu', $groups[3]['label']);
        $this->assertEquals('Tutup', $groups[3]['hours']);
    }

    public function test_branch_page_renders_smart_grouped_schedule_and_toggle(): void
    {
        $branch = Branch::create([
            'name' => 'SAM Bogor',
            'slug' => 'sam-bogor',
            'address' => 'Jl. Pajajaran No. 10',
            'phone_wa' => '081234567899',
            'is_active' => true,
        ]);

        for ($i = 1; $i <= 6; $i++) {
            BranchSchedule::create([
                'branch_id' => $branch->id,
                'day_of_week' => $i,
                'is_open' => true,
                'open_time' => '08:00:00',
                'close_time' => '17:00:00',
            ]);
        }
        BranchSchedule::create([
            'branch_id' => $branch->id,
            'day_of_week' => 7,
            'is_open' => false,
        ]);

        $response = $this->get(route('branch.show', $branch->slug));

        $response->assertOk();
        $response->assertSee('Jadwal Operasional Klinik');
        $response->assertSee('Ringkasan Jam Kerja');
        $response->assertSee('Senin – Sabtu');
        $response->assertSee('08:00 – 17:00 WIB');
    }
}
