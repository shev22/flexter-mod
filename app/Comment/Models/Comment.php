<?php

namespace App\Comment\Models;

use App\Models\User;
use App\Movie\Models\Movie;
use App\Tv\Models\Tv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'media_type',
        'media_id',
        'parent_id',
        'body',
        'is_spoiler',
        'is_flagged',
        'is_blocked',
        'flagged_at',
        'blocked_at',
        'admin_notes',
        'edited_at',
    ];

    protected $casts = [
        'is_spoiler' => 'boolean',
        'is_flagged' => 'boolean',
        'is_blocked' => 'boolean',
        'flagged_at' => 'datetime',
        'blocked_at' => 'datetime',
        'edited_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    /** @param  Builder<self>  $query */
    public function scopeVisible(Builder $query): void
    {
        $query->where('is_blocked', false);
    }

    public function mediaTitle(): string
    {
        $title = match ($this->media_type) {
            'movie' => Movie::query()->find($this->media_id)?->title,
            'tv' => Tv::query()->find($this->media_id)?->title,
            default => null,
        };

        return $title ?? 'Unknown title';
    }

    public function mediaUrl(): ?string
    {
        $title = $this->mediaTitle();

        if ($title === 'Unknown title') {
            return null;
        }

        $slug = Str::slug($title);

        return match ($this->media_type) {
            'movie' => route('movie.show', ['slug' => $slug, 'id' => $this->media_id]),
            'tv' => route('tv.show', ['slug' => $slug, 'id' => $this->media_id]),
            default => null,
        };
    }

    public function mediaTypeLabel(): string
    {
        return match ($this->media_type) {
            'movie' => 'Movie',
            'tv' => 'TV show',
            default => ucfirst($this->media_type),
        };
    }
}
