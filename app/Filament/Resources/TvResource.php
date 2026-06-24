<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TvResource\Pages;
use App\Shared\Support\Tmdb;
use App\Tv\Models\Tv;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TvResource extends Resource
{
    protected static ?string $model = Tv::class;

    protected static ?string $navigationIcon = 'heroicon-o-tv';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'TV Show';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
            Forms\Components\Textarea::make('overview')->rows(4)->columnSpanFull(),
            Forms\Components\Select::make('category')->options(config('categories'))->required(),
            Forms\Components\TextInput::make('release_date'),
            Forms\Components\TextInput::make('vote_average')->numeric(),
            Forms\Components\TextInput::make('vote_count')->numeric(),
            Forms\Components\TextInput::make('popularity'),
            Forms\Components\TextInput::make('original_language')->maxLength(8),
            Forms\Components\Toggle::make('is_trending'),
            Forms\Components\TextInput::make('poster_path')->columnSpanFull(),
            Forms\Components\TextInput::make('backdrop_path')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('poster_path')
                    ->label('Poster')
                    ->getStateUsing(fn (Tv $record) => Tmdb::image($record->poster_path, 'poster'))
                    ->height(64),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->limit(40),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => config('categories')[$state] ?? $state),
                Tables\Columns\TextColumn::make('vote_average')->label('Rating')->badge()->sortable(),
                Tables\Columns\IconColumn::make('is_trending')->boolean(),
                Tables\Columns\TextColumn::make('release_date')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->options(config('categories')),
                Tables\Filters\TernaryFilter::make('is_trending'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('popularity', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTvShows::route('/'),
            'edit' => Pages\EditTvShow::route('/{record}/edit'),
        ];
    }
}
