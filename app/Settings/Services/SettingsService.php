<?php

namespace App\Settings\Services;

use App\Models\User;
use App\Settings\Models\UserSetting;
use App\Settings\Services\Interfaces\SettingsServiceInterface;
use App\Shared\Data\SettingsData;
use App\Shared\Support\HomeCache;
use Illuminate\Support\Facades\Hash;

class SettingsService implements SettingsServiceInterface
{
    public function forUser(User $user): UserSetting
    {
        return $user->settings()->firstOrCreate([], SettingsData::defaults()->toModelArray());
    }

    public function update(User $user, array $attributes): UserSetting
    {
        $setting = $this->forUser($user);
        $setting->fill($attributes)->save();

        if (array_key_exists('favorite_genre_ids', $attributes)) {
            HomeCache::bust();
        }

        return $setting;
    }

    public function updateProfile(User $user, array $attributes): User
    {
        $user->name = $attributes['name'];
        $user->email = $attributes['email'];

        if (! empty($attributes['password'])) {
            $user->password = Hash::make($attributes['password']);
        }

        $user->save();

        return $user;
    }
}
