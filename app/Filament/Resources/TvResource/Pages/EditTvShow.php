<?php

namespace App\Filament\Resources\TvResource\Pages;

use App\Filament\Resources\TvResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTvShow extends EditRecord
{
    protected static string $resource = TvResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
