<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Actor\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor query()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperActor {}
}

namespace App\Genre\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre query()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGenre {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Movie\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie query()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMovie {}
}

namespace App\Series\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Series newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Series newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Series query()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSeries {}
}

namespace App\TopRated\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated query()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTopRated {}
}

namespace App\WishList\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WishList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WishList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WishList query()
 * @mixin Eloquent
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWishList {}
}

