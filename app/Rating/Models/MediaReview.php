<?php

namespace App\Rating\Models;

use App\Models\User;
use App\Movie\Models\Movie;
use App\Tv\Models\Tv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaReview extends Model
{
    protected $fillable = [
        'user_id',
        'media_type',
        'media_id',
        'rating',
        'body',
        'watched_on',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'watched_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolveMedia(): Movie|Tv|null
    {
        return match ($this->media_type) {
            'tv' => Tv::query()->find($this->media_id),
            default => Movie::query()->find($this->media_id),
        };
    }
}
