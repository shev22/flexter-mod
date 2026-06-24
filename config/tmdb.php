<?php

return [
    'token' => env('TMDB_TOKEN'),

    'base_url' => env('TMDB_BASE_URL', 'https://api.themoviedb.org/3'),

    'image_base' => env('TMDB_IMAGE_BASE', 'https://image.tmdb.org/t/p'),

    /*
     | Named image size presets mapped to TMDB size tokens. DTOs reference
     | these keys so image dimensions can be tuned in one place.
     */
    'images' => [
        'poster' => 'w500',
        'poster_lg' => 'w780',
        'backdrop' => 'w1280',
        'backdrop_xl' => 'original',
        'logo' => 'w500',
        'profile' => 'w342',
        'profile_lg' => 'h632',
        'still' => 'w300',
    ],
];
