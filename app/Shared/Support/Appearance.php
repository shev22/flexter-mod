<?php

namespace App\Shared\Support;

final class Appearance
{
    /** @var list<string> */
    public const ACCENTS = [
        'aurora',
        'sunset',
        'emerald',
        'crimson',
        'ocean',
        'gold',
        'cobalt',
        'midnight',
        'forest',
        'wine',
        'plum',
        'navy',
        'slate',
        'rust',
        'teal',
        'bronze',
        'charcoal',
        'moss',
        // legacy — kept for saved preferences
        'lavender',
        'mint',
        'amber',
        'peach',
    ];

    /** @var list<string> */
    public const THEMES = ['dark', 'light', 'system'];

    /** @var list<string> */
    public const DENSITIES = ['compact', 'comfortable', 'spacious'];

    /** @var list<string> */
    public const LANGUAGES = ['en', 'es', 'fr', 'de'];

    /** @var list<string> */
    public const MATURITY = ['all', 'teen', 'mature'];
}
