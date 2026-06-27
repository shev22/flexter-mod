<?php

namespace App\Shared\Data;

use App\Settings\Models\UserSetting;
use App\Shared\Support\Appearance;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

final class SettingsData implements Arrayable, JsonSerializable
{
    public function __construct(
        public string $theme,
        public string $accent,
        public bool $autoplayTrailers,
        public bool $reduceMotion,
        public bool $subtitles,
        public bool $allowAdult,
        public string $density,
        public bool $highContrast,
        public string $language,
        public bool $emailNotifications,
        public bool $spoilerFree,
        /** @var array<int> */
        public array $favoriteGenreIds,
    ) {}

    public static function defaults(): self
    {
        return new self(
            theme: 'dark',
            accent: 'aurora',
            autoplayTrailers: true,
            reduceMotion: false,
            subtitles: true,
            allowAdult: false,
            density: 'comfortable',
            highContrast: false,
            language: 'en',
            emailNotifications: true,
            spoilerFree: false,
            favoriteGenreIds: [],
        );
    }

    public static function fromModel(UserSetting $setting): self
    {
        $defaults = self::defaults();

        return new self(
            theme: $setting->theme ?? $defaults->theme,
            accent: $setting->accent ?? $defaults->accent,
            autoplayTrailers: $setting->autoplay_trailers === null
                ? $defaults->autoplayTrailers
                : (bool) $setting->autoplay_trailers,
            reduceMotion: $setting->reduce_motion === null
                ? $defaults->reduceMotion
                : (bool) $setting->reduce_motion,
            subtitles: $setting->subtitles === null
                ? $defaults->subtitles
                : (bool) $setting->subtitles,
            allowAdult: $setting->allow_adult === null
                ? $defaults->allowAdult
                : (bool) $setting->allow_adult,
            density: $setting->density ?? $defaults->density,
            highContrast: $setting->high_contrast === null
                ? $defaults->highContrast
                : (bool) $setting->high_contrast,
            language: $setting->language ?? $defaults->language,
            emailNotifications: $setting->email_notifications === null
                ? $defaults->emailNotifications
                : (bool) $setting->email_notifications,
            spoilerFree: $setting->spoiler_free === null
                ? $defaults->spoilerFree
                : (bool) $setting->spoiler_free,
            favoriteGenreIds: self::normalizeGenreIds($setting->favorite_genre_ids ?? null),
        );
    }

    /**
     * @return array<int>
     */
    private static function normalizeGenreIds(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('intval', $value))));
    }

    /** @return list<array{value: string, label: string, from: string, to: string, solid?: bool}> */
    public static function accentOptions(): array
    {
        return [
            // Bright & vivid
            ['value' => 'aurora', 'label' => 'Aurora', 'from' => '#a855f7', 'to' => '#d946ef'],
            ['value' => 'sunset', 'label' => 'Sunset', 'from' => '#f97316', 'to' => '#f43f5e'],
            ['value' => 'emerald', 'label' => 'Emerald', 'from' => '#10b981', 'to' => '#14b8a6'],
            ['value' => 'crimson', 'label' => 'Crimson', 'from' => '#f43f5e', 'to' => '#be185d'],
            ['value' => 'ocean', 'label' => 'Ocean', 'from' => '#38bdf8', 'to' => '#6366f1'],
            ['value' => 'gold', 'label' => 'Gold', 'from' => '#f59e0b', 'to' => '#eab308'],
            ['value' => 'cobalt', 'label' => 'Cobalt', 'from' => '#3b82f6', 'to' => '#1d4ed8'],
            ['value' => 'midnight', 'label' => 'Midnight', 'from' => '#6366f1', 'to' => '#312e81'],
            // Deep & solid
            ['value' => 'forest', 'label' => 'Forest', 'from' => '#166534', 'to' => '#14532d', 'solid' => true],
            ['value' => 'wine', 'label' => 'Wine', 'from' => '#881337', 'to' => '#4c0519', 'solid' => true],
            ['value' => 'plum', 'label' => 'Plum', 'from' => '#581c87', 'to' => '#3b0764', 'solid' => true],
            ['value' => 'navy', 'label' => 'Navy', 'from' => '#1e3a8a', 'to' => '#172554', 'solid' => true],
            ['value' => 'slate', 'label' => 'Slate', 'from' => '#334155', 'to' => '#1e293b', 'solid' => true],
            ['value' => 'rust', 'label' => 'Rust', 'from' => '#9a3412', 'to' => '#7c2d12', 'solid' => true],
            ['value' => 'teal', 'label' => 'Teal', 'from' => '#115e59', 'to' => '#0f766e', 'solid' => true],
            ['value' => 'bronze', 'label' => 'Bronze', 'from' => '#92400e', 'to' => '#78350f', 'solid' => true],
            ['value' => 'charcoal', 'label' => 'Charcoal', 'from' => '#374151', 'to' => '#111827', 'solid' => true],
            ['value' => 'moss', 'label' => 'Moss', 'from' => '#3f6212', 'to' => '#365314', 'solid' => true],
        ];
    }

    public function toModelArray(): array
    {
        return [
            'theme' => $this->theme,
            'accent' => $this->accent,
            'autoplay_trailers' => $this->autoplayTrailers,
            'reduce_motion' => $this->reduceMotion,
            'subtitles' => $this->subtitles,
            'allow_adult' => $this->allowAdult,
            'density' => $this->density,
            'high_contrast' => $this->highContrast,
            'language' => $this->language,
            'email_notifications' => $this->emailNotifications,
            'spoiler_free' => $this->spoilerFree,
            'favorite_genre_ids' => $this->favoriteGenreIds,
        ];
    }

    public function toArray(): array
    {
        return $this->toModelArray();
    }

    /** Shared on every Inertia page — preference fields only. */
    public function toSharedArray(): array
    {
        return $this->toModelArray();
    }

    /** Full payload for the Settings page (includes accent/theme pickers). */
    public function toFullArray(): array
    {
        return array_merge($this->toModelArray(), [
            'accents' => self::accentOptions(),
            'themes' => Appearance::THEMES,
            'densities' => Appearance::DENSITIES,
            'languages' => Appearance::LANGUAGES,
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
