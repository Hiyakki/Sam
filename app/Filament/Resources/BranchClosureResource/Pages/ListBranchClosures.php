<?php

namespace App\Filament\Resources\BranchClosureResource\Pages;

use App\Filament\Resources\BranchClosureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranchClosures extends ListRecords
{
    protected static string $resource = BranchClosureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
