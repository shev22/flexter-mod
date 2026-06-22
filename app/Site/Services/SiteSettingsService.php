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
        $payload = Cache::rememberForever(SiteSetting::CACHE_KEY, function (): array {
            $record = SiteSetting::query()->first();

            if ($record === null) {
                $defaults = SiteSettingsData::defaults()->toArray();
                SiteSetting::query()->create(['payload' => $defaults]);

                return $defaults;
            }

            return is_array($record->payload) ? $record->payload : [];
        });

        return SiteSettingsData::fromArray(
            array_merge(SiteSettingsData::defaults()->toArray(), $payload),
        );
    }

    public function update(array $payload): SiteSettingsData
    {
        $settings = SiteSettingsData::fromArray(
            array_merge(SiteSettingsData::defaults()->toArray(), $payload),
        );

        $record = SiteSetting::query()->first();

        if ($record === null) {
            SiteSetting::query()->create(['payload' => $settings->toArray()]);
        } else {
            $record->update(['payload' => $settings->toArray()]);
        }

        Cache::forget(SiteSetting::CACHE_KEY);

        return $this->get();
    }
}
