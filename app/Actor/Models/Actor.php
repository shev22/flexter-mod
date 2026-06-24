<?php

namespace App\Actor\Models;

use App\WatchList\Models\WatchList;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor query()
 * @mixin Eloquent
 * @mixin IdeHelperActor
 */
class Actor extends Model
{
    protected $fillable = ['actor_id', 'name', 'profile_path', 'known_for', 'popularity'];

    public function watchlists(): MorphMany
    {
        return $this->morphMany(WatchList::class, 'media');
    }
}
