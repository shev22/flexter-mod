<?php

namespace App\Shared\Support;

use App\Models\User;
use App\Movie\Models\Movie;
use App\Settings\Models\UserSetting;
use App\Shared\Data\SettingsData;
use App\Tv\Models\Tv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class AdultContent
{
    public static function allowed(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        $user->loadMissing('settings');

        if ($user->settings instanceof UserSetting) {
            return (bool) $user->settings->allow_adult;
        }

        return SettingsData::defaults()->allowAdult;
    }

    public static function includeInApi(): bool
    {
        return self::allowed();
    }

    public static function applyToBuilder(Builder $query): Builder
    {
        if (self::allowed()) {
            return $query;
        }

        return $query->where('adult', false);
    }

    /**
     * @param  Collection<int, Movie|Tv>  $models
     * @return Collection<int, Movie|Tv>
     */
    public static function filterModels(Collection $models): Collection
    {
        if (self::allowed()) {
            return $models;
        }

        return $models->filter(fn (Movie|Tv $model) => ! (bool) $model->adult)->values();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function filterCards(array $items): array
    {
        if (self::allowed()) {
            return $items;
        }

        return collect($items)
            ->filter(fn (array $item) => ($item['type'] ?? '') === 'person' || ! (bool) ($item['adult'] ?? false))
            ->values()
            ->all();
    }

    /**
     * @param  iterable<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function filterTmdb(iterable $items): array
    {
        if (self::allowed()) {
            return collect($items)->values()->all();
        }

        return collect($items)
            ->filter(fn (array $item) => ! (bool) ($item['adult'] ?? false))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $detail
     */
    public static function allowsDetail(array $detail): bool
    {
        if (self::allowed()) {
            return true;
        }

        return ! (bool) ($detail['adult'] ?? false);
    }
}
