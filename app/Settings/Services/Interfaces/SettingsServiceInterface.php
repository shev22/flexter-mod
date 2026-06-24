<?php

namespace App\Settings\Services\Interfaces;

use App\Models\User;
use App\Settings\Models\UserSetting;

interface SettingsServiceInterface
{
    public function forUser(User $user): UserSetting;

    public function update(User $user, array $attributes): UserSetting;

    public function updateProfile(User $user, array $attributes): User;
}
