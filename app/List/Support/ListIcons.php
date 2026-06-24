<?php

namespace App\List\Support;

final class ListIcons
{
    public const DEFAULT = 'film';

    /** @return array<string, string> */
    public static function options(): array
    {
        $icons = config('list_icons', []);

        return collect($icons)
            ->mapWithKeys(fn (array $def, string $key): array => [$key => (string) ($def['label'] ?? $key)])
            ->all();
    }

    public static function normalize(?string $icon): string
    {
        $icon = $icon ?: self::DEFAULT;

        return array_key_exists($icon, config('list_icons', [])) ? $icon : self::DEFAULT;
    }
}
