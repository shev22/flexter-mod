<?php

namespace App\Tv\Models;

use App\Genre\Models\Genre;
use App\WatchList\Models\WatchList;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tv newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tv newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tv query()
 * @mixin Eloquent
 * @mixin IdeHelperTv
 */
class Tv extends Model
{
    protected $table = 'tv';
    protected $fillable =
        [
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
