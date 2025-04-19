<?php

namespace App\WatchList\Models;

use App\Models\User;
use App\Movie\Models\Movie;
use App\Series\Models\Series;
use App\WishList\Models\IdeHelperWishList;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList query()
 * @mixin Eloquent
 * @property Movie|Series $media
 * @mixin IdeHelperWatchList
 */
class WatchList extends Model
{
    protected $table = 'watch_lists';
    protected $fillable = [
        'user_id',
        'media_id',
        'media_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function media(): MorphTo
    {
        return $this->morphTo();
    }

}
