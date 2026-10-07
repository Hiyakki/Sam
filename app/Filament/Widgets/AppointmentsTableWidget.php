<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AppointmentResource;
use App\Models\Appointment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class AppointmentsTableWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = '5 Janji Terdekat';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->where('appointment_date', '>=', Carbon::today())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderBy('appointment_date')
                    ->orderBy('start_time')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('appointment_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jam')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('patient_name')
                    ->label('Nama Pasien')
                    ->searchable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Kantor'),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Layanan'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'completed',
                        'danger' => 'cancelled',
                        'gray' => 'no_show',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'confirmed' => 'Dikonfirmasi',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        'no_show' => 'Tidak Hadir',
                        default => $state,
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('lihat')
                    ->label('Lihat')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Appointment $record): string => AppointmentResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
