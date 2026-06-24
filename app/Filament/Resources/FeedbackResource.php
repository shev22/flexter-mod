<?php

namespace App\Filament\Resources;

use App\Feedback\Models\Feedback;
use App\Filament\Resources\FeedbackResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Feedback';

    protected static ?string $modelLabel = 'Feedback message';

    protected static ?string $pluralModelLabel = 'Feedback';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->disabled(),
            Forms\Components\TextInput::make('email')
                ->disabled(),
            Forms\Components\TextInput::make('subject')
                ->disabled()
                ->columnSpanFull(),
            Forms\Components\Textarea::make('message')
                ->disabled()
                ->rows(10)
                ->columnSpanFull(),
            Forms\Components\Select::make('category')
                ->options([
                    'general' => 'General',
                    'bug' => 'Bug',
                    'feature' => 'Feature',
                    'content' => 'Content',
                ]),
            Forms\Components\Textarea::make('admin_notes')
                ->label('Internal notes')
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\Placeholder::make('user.name')
                ->label('Linked account')
                ->content(fn (Feedback $record): string => $record->user?->name ?? 'Guest submission'),
            Forms\Components\DateTimePicker::make('read_at')
                ->label('Read at')
                ->disabled(),
            Forms\Components\DateTimePicker::make('created_at')
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('read_at')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('gray')
                    ->falseColor('warning')
                    ->getStateUsing(fn (Feedback $record): bool => ! $record->isUnread()),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('message')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Account')
                    ->placeholder('Guest')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'general' => 'General',
                        'bug' => 'Bug',
                        'feature' => 'Feature',
                        'content' => 'Content',
                    ]),
                Tables\Filters\TernaryFilter::make('read_at')
                    ->label('Status')
                    ->nullable()
                    ->trueLabel('Read')
                    ->falseLabel('Unread')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('read_at'),
                        false: fn ($query) => $query->whereNull('read_at'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedback::route('/'),
            'view' => Pages\ViewFeedback::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Cache::remember('filament.feedback.unread', now()->addMinutes(2), fn () => static::getModel()::query()
            ->whereNull('read_at')
            ->count());

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
