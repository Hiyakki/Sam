<?php

namespace App\Filament\Resources\BranchClosureResource\Pages;

use App\Filament\Resources\BranchClosureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranchClosure extends EditRecord
{
    protected static string $resource = BranchClosureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
