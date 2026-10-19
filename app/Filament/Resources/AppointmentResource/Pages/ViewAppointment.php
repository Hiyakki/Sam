<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Models\Appointment;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAppointment extends ViewRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('whatsapp')
                ->label('Chat WA Pasien')
                ->icon('heroicon-m-chat-bubble-left-right')
                ->color('success')
                ->url(fn (Appointment $record) => AppointmentResource::whatsAppUrl($record))
                ->openUrlInNewTab(),
        ];
    }
}
