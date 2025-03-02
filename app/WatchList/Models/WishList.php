<?php

namespace App\WishList\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WishList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WishList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WishList query()
 * @mixin Eloquent
 * @mixin IdeHelperWishList
 */
class WishList extends Model
{
    protected $fillable =
        [
            'user_id',
            'media_id',
            ];

}
