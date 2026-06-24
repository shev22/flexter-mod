<?php

namespace App\Filament\Resources\CommentResource\Pages;

use App\Comment\Models\Comment;
use App\Comment\Services\Interfaces\CommentServiceInterface;
use App\Filament\Resources\CommentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Cache;

class ViewComment extends ViewRecord
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save_notes')
                ->label('Save notes')
                ->icon('heroicon-o-document-text')
                ->action(function (): void {
                    $this->record->update([
                        'admin_notes' => $this->form->getState()['admin_notes'] ?? null,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Notes saved')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('open_media')
                ->label('View title')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (Comment $record): ?string => $record->mediaUrl())
                ->openUrlInNewTab()
                ->visible(fn (Comment $record): bool => $record->mediaUrl() !== null),
            Actions\Action::make('toggle_flag')
                ->label(fn (Comment $record): string => $record->is_flagged ? 'Unflag' : 'Flag')
                ->icon('heroicon-o-flag')
                ->color(fn (Comment $record): string => $record->is_flagged ? 'gray' : 'warning')
                ->requiresConfirmation()
                ->action(function (Comment $record): void {
                    $service = app(CommentServiceInterface::class);

                    if ($record->is_flagged) {
                        $service->unflag($record);
                    } else {
                        $service->flag($record);
                    }

                    $this->refreshFormData(['is_flagged', 'flagged_at']);
                }),
            Actions\Action::make('toggle_block')
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

                    $this->refreshFormData(['is_blocked', 'blocked_at']);
                }),
            Actions\DeleteAction::make()
                ->using(fn (Comment $record) => app(CommentServiceInterface::class)->adminDelete($record)),
            Actions\RestoreAction::make()
                ->after(fn (Comment $record) => Cache::forget("comments.{$record->media_type}.{$record->media_id}")),
        ];
    }
}
