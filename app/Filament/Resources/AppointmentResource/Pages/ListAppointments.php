<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Models\Appointment;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    public function getTabs(): array
    {
        return [
            'today' => Tab::make('Hari Ini')
                ->icon('heroicon-m-sun')
                ->badge(fn () => Appointment::whereDate('appointment_date', today())->active()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('appointment_date', today())),

            'upcoming' => Tab::make('Mendatang')
                ->icon('heroicon-m-calendar')
                ->badge(fn () => Appointment::whereDate('appointment_date', '>', today())->active()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereDate('appointment_date', '>', today())
                    ->whereIn('status', ['pending', 'confirmed'])),

            'pending' => Tab::make('Menunggu Konfirmasi')
                ->icon('heroicon-m-clock')
                ->badge(fn () => Appointment::where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'all' => Tab::make('Semua'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'today';
    }
}
