<?php

namespace App\Genre\Models;

use App\Movie\Models\Movie;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre query()
 * @mixin Eloquent
 * @mixin IdeHelperGenre
 */
class Genre extends Model
{
    public $timestamps = false;

    protected $fillable = [ 'genre_id','genre' ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class);
    }
}
