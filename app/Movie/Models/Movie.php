<?php

namespace App\Movie\Models;

use App\Genre\Models\Genre;
use App\WatchList\Models\WatchList;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie query()
 * @mixin Eloquent
 * @mixin IdeHelperMovie
 */
class Movie extends Model
{
    protected $fillable =
        [
            'backdrop_path',
            'logo',
            'category',
            'genre_ids',
            'original_language',
            'overview',
            'popularity',
            'poster_path',
            'release_date',
            'is_trending',
            'trailer',
            'title',
            'vote_average',
            'vote_count',
        ];

    /**
     * @return BelongsToMany
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * @return MorphMany
     */
    public function watchlists(): MorphMany
    {
        return $this->morphMany(WatchList::class, 'media');
    }

}
