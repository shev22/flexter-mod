<?php

namespace App\Series\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;

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
            'original_language',
            'overview',
            'popularity',
            'poster_path',
            'release_date',
            'vote_average',
            'vote_count',
            'year',
            'media_type'
        ];

}
