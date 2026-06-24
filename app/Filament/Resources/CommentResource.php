<?php

namespace App\Filament\Resources;

use App\Comment\Models\Comment;
use App\Comment\Services\Interfaces\CommentServiceInterface;
use App\Filament\Resources\CommentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Comments';

    protected static ?string $modelLabel = 'Comment';

    protected static ?string $pluralModelLabel = 'Comments';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Comment')
                ->schema([
                    Forms\Components\Textarea::make('body')
                        ->disabled()
                        ->rows(6)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_spoiler')
                        ->label('Spoiler')
                        ->disabled(),
                    Forms\Components\Toggle::make('is_flagged')
                        ->label('Flagged')
                        ->disabled(),
                    Forms\Components\Toggle::make('is_blocked')
                        ->label('Blocked')
                        ->disabled(),
                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Internal notes')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(3),
            Forms\Components\Section::make('Context')
                ->schema([
                    Forms\Components\Placeholder::make('user.name')
                        ->label('Author')
                        ->content(fn (Comment $record): string => $record->user?->name ?? 'Unknown user'),
                    Forms\Components\Placeholder::make('media')
                        ->label('Title')
                        ->content(fn (Comment $record): string => $record->mediaTypeLabel().': '.$record->mediaTitle()),
                    Forms\Components\Placeholder::make('parent.body')
                        ->label('Reply to')
                        ->content(fn (Comment $record): string => $record->parent
                            ? '#'.$record->parent->id.' — '.str($record->parent->body)->limit(80)
                            : 'Top-level comment'),
                    Forms\Components\Placeholder::make('likes_count')
                        ->label('Likes')
                        ->content(fn (Comment $record): string => (string) $record->likes()->count()),
                    Forms\Components\Placeholder::make('replies_count')
                        ->label('Replies')
                        ->content(fn (Comment $record): string => (string) $record->replies()->count()),
                    Forms\Components\DateTimePicker::make('created_at')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('edited_at')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('flagged_at')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('blocked_at')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('deleted_at')
                        ->label('Deleted at')
                        ->disabled(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_flagged')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-flag')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_blocked')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('danger')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('body')
                    ->searchable()
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('media_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'movie' => 'Movie',
                        'tv' => 'TV',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('media_title')
                    ->label('Title')
                    ->getStateUsing(fn (Comment $record): string => $record->mediaTitle())
                    ->limit(30)
                    ->url(fn (Comment $record): ?string => $record->mediaUrl())
                    ->openUrlInNewTab(),
                Tables\Columns\IconColumn::make('is_spoiler')
                    ->label('Spoiler')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('likes_count')
                    ->counts('likes')
                    ->label('Likes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('replies_count')
                    ->counts('replies')
                    ->label('Replies')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('media_type')
                    ->label('Media type')
                    ->options([
                        'movie' => 'Movie',
                        'tv' => 'TV show',
                    ]),
                Tables\Filters\TernaryFilter::make('is_flagged')
                    ->label('Flagged'),
                Tables\Filters\TernaryFilter::make('is_blocked')
                    ->label('Blocked'),
                Tables\Filters\TernaryFilter::make('is_spoiler')
                    ->label('Spoiler'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('toggle_flag')
                    ->label(fn (Comment $record): string => $record->is_flagged ? 'Unflag' : 'Flag')
                    ->icon(fn (Comment $record): string => $record->is_flagged ? 'heroicon-o-flag' : 'heroicon-o-flag')
                    ->color(fn (Comment $record): string => $record->is_flagged ? 'gray' : 'warning')
                    ->requiresConfirmation()
                    ->action(function (Comment $record): void {
                        $service = app(CommentServiceInterface::class);

                        if ($record->is_flagged) {
                            $service->unflag($record);
                        } else {
                            $service->flag($record);
                        }
                    }),
                Tables\Actions\Action::make('toggle_block')
                    ->label(fn (Comment $record): string => $record->is_blocked ? 'Unblock' : 'Block')
                    ->icon('heroicon-o-no-symbol')
                    ->color(fn (Comment $record): string => $record->is_blocked ? 'gray' : 'danger')
                    ->requiresConfirmation()
                    ->action(function (Comment $record): void {
                        $service = app(CommentServiceInterface::class);

                        if ($record->is_blocked) {
                            $service->unblock($record);
                        } else {
                            $service->block($record);
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->using(fn (Comment $record) => app(CommentServiceInterface::class)->adminDelete($record)),
                Tables\Actions\RestoreAction::make()
                    ->after(fn (Comment $record) => Cache::forget("comments.{$record->media_type}.{$record->media_id}")),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('flag')
                        ->label('Flag selected')
                        ->icon('heroicon-o-flag')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $service = app(CommentServiceInterface::class);

                            $records->each(function (Comment $record) use ($service): void {
                                if (! $record->is_flagged) {
                                    $service->flag($record);
                                }
                            });
                        }),
                    Tables\Actions\BulkAction::make('block')
                        ->label('Block selected')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $service = app(CommentServiceInterface::class);

                            $records->each(function (Comment $record) use ($service): void {
                                if (! $record->is_blocked) {
                                    $service->block($record);
                                }
                            });
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->using(function ($records): void {
                            $service = app(CommentServiceInterface::class);

                            $records->each(fn (Comment $record) => $service->adminDelete($record));
                        }),
                    Tables\Actions\RestoreBulkAction::make()
                        ->after(function ($records): void {
                            $records->each(function (Comment $record): void {
                                Cache::forget("comments.{$record->media_type}.{$record->media_id}");
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user:id,name'])
            ->withCount(['likes', 'replies']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'view' => Pages\ViewComment::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Cache::remember('filament.comments.flagged', now()->addMinutes(2), fn () => static::getModel()::query()
            ->where('is_flagged', true)
            ->whereNull('deleted_at')
            ->count());

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
