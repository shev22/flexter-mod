<?php

namespace App\Settings\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'accent',
        'autoplay_trailers',
        'reduce_motion',
        'subtitles',
        'allow_adult',
        'density',
        'high_contrast',
        'language',
        'email_notifications',
        'spoiler_free',
        'favorite_genre_ids',
        'tonight_queue_started_at',
    ];

    protected $casts = [
        'autoplay_trailers' => 'boolean',
        'reduce_motion' => 'boolean',
        'subtitles' => 'boolean',
        'allow_adult' => 'boolean',
        'high_contrast' => 'boolean',
        'email_notifications' => 'boolean',
        'spoiler_free' => 'boolean',
        'favorite_genre_ids' => 'array',
        'tonight_queue_started_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
