<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache TTLs (seconds)
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'catalogue_ttl' => (int) env('FLEXTER_CACHE_CATALOGUE_TTL', 1800),
        'lists_ttl' => (int) env('FLEXTER_CACHE_LISTS_TTL', 1800),
        'comments_ttl' => (int) env('FLEXTER_CACHE_COMMENTS_TTL', 300),
        'home_ttl' => (int) env('FLEXTER_CACHE_HOME_TTL', 600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Streaming playback
    |--------------------------------------------------------------------------
    |
    | Defaults for the embed player. Admins can override everything from
    | Filament → Streaming Player.
    | VidPlus: https://vidplus.to — VidPhantom: https://vidphantom.com
    |
    */

    'playback' => [
        'enabled' => filter_var(env('FLEXTER_PLAYBACK_ENABLED', true), FILTER_VALIDATE_BOOL),
        'provider' => env('FLEXTER_PLAYBACK_PROVIDER', 'vidplus'),
        'base_url' => rtrim((string) env('FLEXTER_PLAYBACK_BASE_URL', 'https://player.vidplus.to'), '/'),
        'progress_mode' => env('FLEXTER_PLAYBACK_PROGRESS', 'session'),
        'url_style' => env('FLEXTER_PLAYBACK_URL_STYLE', 'embed'),

        // VidPlus playback controls
        'autoplay' => filter_var(env('FLEXTER_PLAYBACK_AUTOPLAY', false), FILTER_VALIDATE_BOOL),
        'autonext' => filter_var(env('FLEXTER_PLAYBACK_AUTONEXT', true), FILTER_VALIDATE_BOOL),
        'next_button' => filter_var(env('FLEXTER_PLAYBACK_NEXT_BUTTON', true), FILTER_VALIDATE_BOOL),
        'resume_from_progress' => filter_var(env('FLEXTER_PLAYBACK_RESUME', true), FILTER_VALIDATE_BOOL),
        'sync_accent_color' => filter_var(env('FLEXTER_PLAYBACK_SYNC_ACCENT', true), FILTER_VALIDATE_BOOL),

        // VidPlus visual theme
        'primary_color' => env('FLEXTER_PLAYBACK_PRIMARY_COLOR', '6C63FF'),
        'secondary_color' => env('FLEXTER_PLAYBACK_SECONDARY_COLOR', '9F9BFF'),
        'icon_color' => env('FLEXTER_PLAYBACK_ICON_COLOR', 'FFFFFF'),
        'poster' => filter_var(env('FLEXTER_PLAYBACK_POSTER', true), FILTER_VALIDATE_BOOL),
        'show_title' => filter_var(env('FLEXTER_PLAYBACK_SHOW_TITLE', true), FILTER_VALIDATE_BOOL),
        'icons' => env('FLEXTER_PLAYBACK_ICONS', 'default'),

        // VidPlus UI features
        'server_icon' => filter_var(env('FLEXTER_PLAYBACK_SERVER_ICON', true), FILTER_VALIDATE_BOOL),
        'setting' => filter_var(env('FLEXTER_PLAYBACK_SETTING', true), FILTER_VALIDATE_BOOL),
        'pip' => filter_var(env('FLEXTER_PLAYBACK_PIP', true), FILTER_VALIDATE_BOOL),
        'episode_list' => filter_var(env('FLEXTER_PLAYBACK_EPISODE_LIST', true), FILTER_VALIDATE_BOOL),
        'chromecast' => filter_var(env('FLEXTER_PLAYBACK_CHROMECAST', true), FILTER_VALIDATE_BOOL),
        'watchparty' => filter_var(env('FLEXTER_PLAYBACK_WATCHPARTY', false), FILTER_VALIDATE_BOOL),
        'download' => filter_var(env('FLEXTER_PLAYBACK_DOWNLOAD', false), FILTER_VALIDATE_BOOL),

        // Subtitles & font
        'font' => env('FLEXTER_PLAYBACK_FONT', 'Roboto'),
        'font_color' => env('FLEXTER_PLAYBACK_FONT_COLOR', 'FFFFFF'),
        'font_size' => (int) env('FLEXTER_PLAYBACK_FONT_SIZE', 20),
        'font_opacity' => (float) env('FLEXTER_PLAYBACK_FONT_OPACITY', 0.5),

        // Advanced
        'logo_url' => env('FLEXTER_PLAYBACK_LOGO_URL', ''),
        'server' => env('FLEXTER_PLAYBACK_SERVER', ''),

        // Legacy VidSrc env alias
        'vidsrc_base_url' => rtrim((string) env('VIDSRC_BASE_URL', 'https://vidsrc.to'), '/'),
    ],

];
