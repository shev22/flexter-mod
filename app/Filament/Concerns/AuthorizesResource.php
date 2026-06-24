<?php

namespace App\Filament\Concerns;

use App\Enums\Permission;

trait AuthorizesResource
{
    protected static function resourcePermission(): Permission
    {
        throw new \LogicException(static::class.' must define a resource permission.');
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->can(static::resourcePermission()->value);
    }

    public static function canView($record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }
}
