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
 * @property int $id
 * @property string $name
 * @property string|null $profile_path
 * @property string $known_for
 * @property string $popularity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor whereKnownFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor wherePopularity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor whereProfilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Actor whereUpdatedAt($value)
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
 * @property int $id
 * @property string $genre
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Movie\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre whereGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre whereId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGenre {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\WatchList\Models\WatchList> $watchlist
 * @property-read int|null $watchlist_count
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
 * @property int $id
 * @property string|null $backdrop_path
 * @property string|null $title
 * @property string|null $genre_ids
 * @property string|null $logo
 * @property string $category
 * @property string|null $original_language
 * @property string|null $overview
 * @property string|null $popularity
 * @property string|null $poster_path
 * @property string|null $release_date
 * @property string $is_trending
 * @property string|null $trailer
 * @property string|null $runtime
 * @property string|null $vote_average
 * @property string|null $vote_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Genre\Models\Genre> $genres
 * @property-read int|null $genres_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\WatchList\Models\WatchList> $watchlists
 * @property-read int|null $watchlists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereBackdropPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereGenreIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereIsTrending($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereOriginalLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereOverview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie wherePopularity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie wherePosterPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereReleaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereRuntime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereTrailer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereVoteAverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereVoteCount($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMovie {}
}

namespace App\TopRated\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated query()
 * @mixin Eloquent
 * @property int $id
 * @property int $series_id
 * @property string|null $backdrop_path
 * @property string|null $title
 * @property string|null $logo
 * @property string|null $original_language
 * @property string|null $overview
 * @property string|null $popularity
 * @property string|null $poster_path
 * @property string|null $release_date
 * @property string|null $vote_average
 * @property string|null $vote_count
 * @property string|null $year
 * @property string $media_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereBackdropPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereOriginalLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereOverview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated wherePopularity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated wherePosterPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereReleaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereSeriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereVoteAverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereVoteCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopRated whereYear($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTopRated {}
}

namespace App\Tv\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tv newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tv newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tv query()
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Genre\Models\Genre> $genres
 * @property-read int|null $genres_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\WatchList\Models\WatchList> $watchlists
 * @property-read int|null $watchlists_count
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTv {}
}

namespace App\WatchList\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList query()
 * @mixin Eloquent
 * @property Movie|Tv $media
 * @property int $id
 * @property int $user_id
 * @property string $media_type
 * @property int $media_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchList whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperWatchList {}
}

