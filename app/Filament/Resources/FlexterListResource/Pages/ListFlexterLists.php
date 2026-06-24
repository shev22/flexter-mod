<?php

namespace App\Filament\Resources\FlexterListResource\Pages;

use App\Filament\Resources\FlexterListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlexterLists extends ListRecords
{
    protected static string $resource = FlexterListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
