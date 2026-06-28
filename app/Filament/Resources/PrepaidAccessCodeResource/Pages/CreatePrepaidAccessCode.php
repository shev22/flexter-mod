<?php

namespace App\Filament\Resources\PrepaidAccessCodeResource\Pages;

use App\Billing\Services\PrepaidCodeService;
use App\Filament\Resources\PrepaidAccessCodeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePrepaidAccessCode extends CreateRecord
{
    protected static string $resource = PrepaidAccessCodeResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $code = app(PrepaidCodeService::class)->generateCode(
            (int) ($data['duration_days'] ?? 30),
            $data['label'] ?? null,
            auth()->user(),
        );

        Notification::make()
            ->title('Access code created')
            ->body($code->code)
            ->success()
            ->send();

        return $code;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
