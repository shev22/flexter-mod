<?php

namespace App\Filament\Resources\FlexterListResource\RelationManagers;

use App\List\Models\FlexterListItem;
use App\Shared\Support\Tmdb;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Generated titles';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->required()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('poster')
                    ->label('Poster')
                    ->getStateUsing(function (FlexterListItem $record): ?string {
                        $media = $record->resolveMedia();

                        return $media ? Tmdb::image($media->poster_path, 'poster') : null;
                    })
                    ->height(72),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->getStateUsing(fn (FlexterListItem $record): string => $record->resolveMedia()?->title ?? 'Missing from catalogue')
                    ->wrap(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->getStateUsing(fn (FlexterListItem $record): string => number_format((float) ($record->resolveMedia()?->vote_average ?? 0), 1)),
                Tables\Columns\TextColumn::make('year')
                    ->label('Year')
                    ->getStateUsing(fn (FlexterListItem $record): string => Tmdb::year($record->resolveMedia()?->release_date) ?? '—'),
                Tables\Columns\TextColumn::make('media_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'tv' ? 'Series' : 'Movie'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Reorder')
                    ->modalHeading('Change display order'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No titles generated yet')
            ->emptyStateDescription('Save the list rules and use “Regenerate titles” to populate this list from the catalogue.');
    }
}
