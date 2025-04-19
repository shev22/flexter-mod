<?php

namespace App\Series\Models;

use App\Genre\Models\Genre;
use App\WatchList\Models\WatchList;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Series newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Series newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Series query()
 * @mixin Eloquent
 * @mixin IdeHelperSeries
 */
class Series extends Model
{
    protected $fillable =
        [
            'series_id',
            'backdrop_path',
            'title',
            'logo',
            'category',
            'original_language',
            'overview',
            'popularity',
            'poster_path',
            'release_date',
            'is_trending',
            'vote_average',
            'vote_count',
            'year',
        ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function watchlists(): MorphMany
    {
        return $this->morphMany(WatchList::class, 'media');
    }

}
