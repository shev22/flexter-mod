<?php

namespace App\Shared\Support;

use Carbon\Carbon;

/**
 * Small presentation helper that turns raw TMDB path segments into fully
 * qualified, size-aware URLs and normalises a few recurring values. Centralising
 * this keeps the DTO factories declarative.
 */
final class Tmdb
{
    public static function image(?string $path, string $size = 'poster'): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        $sizes = config('tmdb.images', []);
        $segment = $sizes[$size] ?? $size;

        return rtrim((string) config('tmdb.image_base'), '/').'/'.$segment.'/'.ltrim($path, '/');
    }

    public static function youtube(?string $key): ?string
    {
        $id = self::youtubeKey($key);

        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }

    /** Normalize a TMDB/YouTube value into an 11-char video id. */
    public static function youtubeKey(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $value)) {
            return $value;
        }

        if (preg_match('/(?:youtu\.be\/|v=|embed\/)([a-zA-Z0-9_-]{11})/', $value, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $videos
     */
    public static function pickTrailer(array $videos): ?string
    {
        foreach ($videos as $video) {
            if (strcasecmp((string) ($video['site'] ?? ''), 'YouTube') !== 0 || empty($video['key'])) {
                continue;
            }

            if (($video['type'] ?? null) === 'Trailer') {
                return self::youtubeKey((string) $video['key']);
            }
        }

        foreach ($videos as $video) {
            if (strcasecmp((string) ($video['site'] ?? ''), 'YouTube') !== 0 || empty($video['key'])) {
                continue;
            }

            if (in_array($video['type'] ?? '', ['Teaser', 'Clip'], true)) {
                return self::youtubeKey((string) $video['key']);
            }
        }

        foreach ($videos as $video) {
            if (strcasecmp((string) ($video['site'] ?? ''), 'YouTube') === 0 && ! empty($video['key'])) {
                return self::youtubeKey((string) $video['key']);
            }
        }

        return null;
    }

    public static function year(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y');
        } catch (\Throwable) {
            return null;
        }
    }

    /** Human-readable release label, e.g. "Jul, 2024". */
    public static function monthYear(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('M, Y');
        } catch (\Throwable) {
            return null;
        }
    }

    public static function rating(mixed $value): float
    {
        $value = (float) $value;

        return $value < 1 ? 0.0 : round($value, 1);
    }

    /**
     * Resolve a list of human readable genre names from either an array/JSON of
     * TMDB genre ids or a TMDB "genres" array of {id,name} objects.
     *
     * @return array<int, string>
     */
    public static function genreNames(mixed $genreIds = null, ?array $genres = null): array
    {
        if (! empty($genres)) {
            return collect($genres)->pluck('name')->filter()->values()->all();
        }

        if (empty($genreIds)) {
            return [];
        }

        $ids = is_array($genreIds) ? $genreIds : (json_decode((string) $genreIds, true) ?: []);
        $map = config('genres', []);

        return collect($ids)
            ->map(fn ($id) => $map[$id] ?? null)
            ->filter()
            ->values()
            ->all();
    }
}
