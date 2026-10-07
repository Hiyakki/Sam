<?php

namespace App\Filament\Resources\BranchClosureResource\Pages;

use App\Filament\Resources\BranchClosureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBranchClosure extends CreateRecord
{
    protected static string $resource = BranchClosureResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
