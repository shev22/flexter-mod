<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache TTLs (seconds)
    |--------------------------------------------------------------------------
    |
    | Versioned buckets invalidate instantly when catalogue sync or list edits
    | bump the version counter — no need to flush individual keys.
    |
    */

    'cache' => [
        'catalogue_ttl' => (int) env('FLEXTER_CACHE_CATALOGUE_TTL', 1800),
        'lists_ttl' => (int) env('FLEXTER_CACHE_LISTS_TTL', 1800),
        'comments_ttl' => (int) env('FLEXTER_CACHE_COMMENTS_TTL', 300),
        'home_ttl' => (int) env('FLEXTER_CACHE_HOME_TTL', 600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Streaming playback (Vidsrc)
    |--------------------------------------------------------------------------
    |
    | Watch now embeds third-party iframes. TMDB IDs from the catalogue are passed
    | directly to the player. Defaults to vidsrc.to — see https://vidsrc.to/
    |
    */

    'playback' => [
        'enabled' => filter_var(env('FLEXTER_PLAYBACK_ENABLED', true), FILTER_VALIDATE_BOOL),
        'provider' => env('FLEXTER_PLAYBACK_PROVIDER', 'vidsrc.to'),
        'base_url' => rtrim((string) env('VIDSRC_BASE_URL', 'https://vidsrc.to'), '/'),
        // embed = vidsrc.to style (/embed/movie/{id}), legacy = vidsrc.ru style (/movie/{id})
        'url_style' => env('FLEXTER_PLAYBACK_URL_STYLE', 'embed'),
        // session = Flexter estimates progress from watch time; postmessage = vidsrc.ru MEDIA_DATA events
        'progress_mode' => env('FLEXTER_PLAYBACK_PROGRESS', 'session'),
    ],

];
