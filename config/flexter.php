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

];
