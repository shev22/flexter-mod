<?php

namespace App\Filament\Resources;

use App\Billing\Models\PrepaidAccessCode;
use App\Filament\Resources\PrepaidAccessCodeResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PrepaidAccessCodeResource extends Resource
{
    protected static ?string $model = PrepaidAccessCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Access codes';

    protected static ?string $modelLabel = 'Access code';

    protected static ?string $pluralModelLabel = 'Access codes';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->disabled()
                ->dehydrated(false)
                ->visibleOn('edit'),
            Forms\Components\TextInput::make('duration_days')
                ->label('Duration (days)')
                ->numeric()
                ->minValue(1)
                ->maxValue(3650)
                ->default(30)
                ->required(),
            Forms\Components\TextInput::make('label')
                ->placeholder('Crypto promo, gift card, etc.')
                ->maxLength(120),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('duration_days')
                    ->label('Days')
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('redeemedBy.name')
                    ->label('Redeemed by')
                    ->placeholder('Unused'),
                Tables\Columns\TextColumn::make('redeemed_at')
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrepaidAccessCodes::route('/'),
            'create' => Pages\CreatePrepaidAccessCode::route('/create'),
            'view' => Pages\ViewPrepaidAccessCode::route('/{record}'),
        ];
    }

    public static function canEdit($record): bool
    {
        return false;
    }
}
