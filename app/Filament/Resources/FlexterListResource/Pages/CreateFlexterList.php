<?php

namespace App\Filament\Resources\FlexterListResource\Pages;

use App\Filament\Resources\FlexterListResource;
use App\List\Services\ListGeneratorService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateFlexterList extends CreateRecord
{
    protected static string $resource = FlexterListResource::class;

    protected function afterCreate(): void
    {
        $count = app(ListGeneratorService::class)->generate($this->record);

        Notification::make()
            ->title('List created')
            ->body($count > 0
                ? "Added {$count} title(s) from the catalogue."
                : 'No matching titles were found. Try different genres or lower the rating/year filters.')
            ->success()
            ->send();
    }
}
