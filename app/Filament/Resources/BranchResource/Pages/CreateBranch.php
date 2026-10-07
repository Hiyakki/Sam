<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use App\Models\BranchSchedule;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Setelah cabang dibuat, otomatis buat 7 jadwal (Senin-Minggu)
     * agar repeater jadwal bisa langsung diisi saat edit.
     */
    protected function afterCreate(): void
    {
        $branch = $this->record;
        for ($day = 1; $day <= 7; $day++) {
            BranchSchedule::firstOrCreate(
                ['branch_id' => $branch->id, 'day_of_week' => $day],
                [
                    'is_open'               => $day <= 6,
                    'open_time'             => $day <= 6 ? '08:00' : null,
                    'close_time'            => $day <= 6 ? '17:00' : null,
                    'slot_duration_minutes' => 60,
                    'capacity_per_slot'     => 1,
                ]
            );
        }
    }
}
