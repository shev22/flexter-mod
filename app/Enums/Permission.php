<?php

namespace App\Enums;

enum Permission: string
{
    case AccessAdminPanel = 'access admin panel';
    case ManageMovies = 'manage movies';
    case ManageTv = 'manage tv';
    case ManageActors = 'manage actors';
    case ManageGenres = 'manage genres';
    case ManageUsers = 'manage users';
    case ViewTmdbApiActivity = 'view tmdb api activity';

    public function getLabel(): string
    {
        return match ($this) {
            self::AccessAdminPanel => 'Access Admin Panel',
            self::ManageMovies => 'Manage Movies',
            self::ManageTv => 'Manage TV Shows',
            self::ManageActors => 'Manage Actors',
            self::ManageGenres => 'Manage Genres',
            self::ManageUsers => 'Manage Users',
            self::ViewTmdbApiActivity => 'View TMDB API Activity',
        };
    }
}
