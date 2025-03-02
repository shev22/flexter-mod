<?php

namespace App\Genre\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre query()
 * @mixin Eloquent
 * @mixin IdeHelperGenre
 */
class Genre extends Model
{
    protected $fillable = [ 'genre_id','genre' ];
}
