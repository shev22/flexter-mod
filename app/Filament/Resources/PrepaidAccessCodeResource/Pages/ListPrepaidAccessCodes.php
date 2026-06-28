<?php

namespace App\Filament\Resources\PrepaidAccessCodeResource\Pages;

use App\Filament\Resources\PrepaidAccessCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrepaidAccessCodes extends ListRecords
{
    protected static string $resource = PrepaidAccessCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
