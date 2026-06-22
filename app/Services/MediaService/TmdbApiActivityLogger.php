<?php

namespace App\Services\MediaService;

use App\Jobs\LogTmdbApiActivity;
use App\Models\TmdbApiActivity;
use Illuminate\Support\Facades\Cache;

class TmdbApiActivityLogger
{
    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function log(
        string $operation,
        ?string $mediaType,
        int $requestCount,
        int $itemsFetched,
        string $status = 'success',
        ?string $errorMessage = null,
        ?string $category = null,
        ?array $metadata = null,
    ): void {
        $payload = [
            'operation' => $operation,
            'media_type' => $mediaType,
            'category' => $category,
            'request_count' => $requestCount,
            'items_fetched' => $itemsFetched,
            'source' => app()->runningInConsole() ? 'cli' : 'web',
            'status' => $status,
            'error_message' => $errorMessage,
            'metadata' => $metadata,
            'created_at' => now(),
        ];

        if (app()->runningInConsole()) {
            TmdbApiActivity::query()->create($payload);
            Cache::forget('tmdb.requests.today');

            return;
        }

        LogTmdbApiActivity::dispatch($payload);
    }
}
