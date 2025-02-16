<?php

namespace App\Actor\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor query()
 * @mixin Eloquent
 * @mixin IdeHelperActor
 */
class Actor extends Model
{
    protected $fillable = [ 'actor_id', 'name', 'profile_path', 'known_for', 'popularity','slug' ];

}
