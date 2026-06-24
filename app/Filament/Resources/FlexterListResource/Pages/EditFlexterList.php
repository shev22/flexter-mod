<?php

namespace App\Filament\Resources\FlexterListResource\Pages;

use App\Filament\Resources\FlexterListResource;
use App\List\Services\ListGeneratorService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFlexterList extends EditRecord
{
    protected static string $resource = FlexterListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('regenerate')
                ->label('Regenerate titles')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalDescription('This replaces all current titles with a fresh pick from the catalogue using the rules below.')
                ->action(function (): void {
                    $count = app(ListGeneratorService::class)->generate($this->record);

                    Notification::make()
                        ->title('List regenerated')
                        ->body($count > 0
                            ? "Added {$count} title(s) from the catalogue."
                            : 'No matching titles were found. Adjust genres, rating, or year filters.')
                        ->success()
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
