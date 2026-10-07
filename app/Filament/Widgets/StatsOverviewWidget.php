<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();

        $todayCount = Appointment::whereDate('appointment_date', $today)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->count();

        $pendingCount = Appointment::where('status', 'pending')->count();

        $upcomingCount = Appointment::where('appointment_date', '>=', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        return [
            Stat::make('Janji Hari Ini', $todayCount)
                ->description('Janji yang tidak dibatalkan hari ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('Menunggu Konfirmasi', $pendingCount)
                ->description('Janji berstatus pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Janji Mendatang', $upcomingCount)
                ->description('Pending & dikonfirmasi ke depan')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
        ];
    }
}
