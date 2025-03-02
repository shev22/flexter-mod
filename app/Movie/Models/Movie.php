<?php

namespace App\Movie\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;

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
            'movie_id',
            'backdrop_path',
            'logo',
            'original_language',
            'overview',
            'popularity',
            'poster_path',
            'release_date',
            'title',
            'vote_average',
            'vote_count',
            'year',
            'media_type'
        ];
}
