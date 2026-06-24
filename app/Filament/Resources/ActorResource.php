<?php

namespace App\Filament\Resources;

use App\Actor\Models\Actor;
use App\Filament\Resources\ActorResource\Pages;
use App\Shared\Support\Tmdb;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActorResource extends Resource
{
    protected static ?string $model = Actor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('known_for')->columnSpanFull(),
            Forms\Components\TextInput::make('popularity'),
            Forms\Components\TextInput::make('profile_path')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_path')
                    ->label('Photo')
                    ->circular()
                    ->getStateUsing(fn (Actor $record) => Tmdb::image($record->profile_path, 'profile')),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('known_for')->limit(50)->wrap(),
                Tables\Columns\TextColumn::make('popularity')->sortable(),
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
            'index' => Pages\ListActors::route('/'),
            'edit' => Pages\EditActor::route('/{record}/edit'),
        ];
    }
}
