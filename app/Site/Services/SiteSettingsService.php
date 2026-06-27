<?php

namespace App\Site\Services;

use App\Site\Data\SiteSettingsData;
use App\Site\Models\SiteSetting;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use Illuminate\Support\Facades\Cache;

class SiteSettingsService implements SiteSettingsServiceInterface
{
    public function get(): SiteSettingsData
    {
        return SiteSettingsData::fromArray($this->getPayload());
    }

    public function getPayload(): array
    {
        $payload = Cache::rememberForever(SiteSetting::CACHE_KEY, function (): array {
            $record = SiteSetting::query()->first();

            if ($record === null) {
                $defaults = SiteSettingsData::defaults()->toArray();
                SiteSetting::query()->create(['payload' => $defaults]);

                return $defaults;
            }

            return is_array($record->payload) ? $record->payload : [];
        });

        return array_merge(SiteSettingsData::defaults()->toArray(), $payload);
    }

    public function update(array $payload): SiteSettingsData
    {
        $record = SiteSetting::query()->first();
        $existing = is_array($record?->payload) ? $record->payload : [];

        $merged = array_merge(SiteSettingsData::defaults()->toArray(), $existing, $payload);
        $settings = SiteSettingsData::fromArray($merged);
        $stored = array_merge($merged, $settings->toArray());

        if ($record === null) {
            SiteSetting::query()->create(['payload' => $stored]);
        } else {
            $record->update(['payload' => $stored]);
        }

        Cache::forget(SiteSetting::CACHE_KEY);

        return $this->get();
    }
}
