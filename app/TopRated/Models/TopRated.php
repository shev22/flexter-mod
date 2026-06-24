<?php

namespace App\TopRated\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated query()
 * @mixin Eloquent
 * @mixin IdeHelperTopRated
 */
class TopRated extends Model
{
    protected $table = 'top_rated';
    protected $fillable =
        [
            'id',
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
