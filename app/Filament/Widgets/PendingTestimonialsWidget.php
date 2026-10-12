<?php

namespace App\Filament\Widgets;

use App\Models\Testimonial;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingTestimonialsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pendingCount = Testimonial::where('status', 'pending')->count();

        if ($pendingCount === 0) {
            return []; // Hide if none
        }

        return [
            Stat::make('Testimoni Menunggu Persetujuan', $pendingCount)
                ->description('Ada testimoni baru yang perlu ditinjau.')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url(route('filament.kelola-sam.resources.testimonials.index', ['tableFilters[status][value]' => 'pending'])),
        ];
    }
}
