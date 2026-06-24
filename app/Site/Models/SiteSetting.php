<?php

namespace App\Site\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    public const CACHE_KEY = 'site_settings.payload';

    protected $fillable = [
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
