<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchSchedule;
use Illuminate\Database\Seeder;

class BranchScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('slug', 'sam-bogor')->firstOrFail();

        // 1=Senin, 2=Selasa, ..., 7=Minggu
        $schedules = [
            ['day_of_week' => 1, 'is_open' => true,  'open_time' => '08:00', 'close_time' => '17:00'], // Senin
            ['day_of_week' => 2, 'is_open' => true,  'open_time' => '08:00', 'close_time' => '17:00'], // Selasa
            ['day_of_week' => 3, 'is_open' => true,  'open_time' => '08:00', 'close_time' => '17:00'], // Rabu
            ['day_of_week' => 4, 'is_open' => true,  'open_time' => '08:00', 'close_time' => '17:00'], // Kamis
            ['day_of_week' => 5, 'is_open' => true,  'open_time' => '08:00', 'close_time' => '17:00'], // Jumat
            ['day_of_week' => 6, 'is_open' => true,  'open_time' => '08:00', 'close_time' => '17:00'], // Sabtu
            ['day_of_week' => 7, 'is_open' => false, 'open_time' => null,    'close_time' => null],    // Minggu
        ];

        foreach ($schedules as $schedule) {
            BranchSchedule::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'day_of_week' => $schedule['day_of_week'],
                ],
                [
                    'is_open' => $schedule['is_open'],
                    'open_time' => $schedule['open_time'],
                    'close_time' => $schedule['close_time'],
                    'slot_duration_minutes' => 60,
                    'capacity_per_slot' => 1,
                ]
            );
        }
    }
}
