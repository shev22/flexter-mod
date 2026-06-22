<?php

namespace App\WatchList\Services;

use App\Enums\Categories;
use App\Models\User;
use App\Movie\Models\Movie;
use App\Movie\Repositories\Interfaces\MovieRepositoryInterface;
use App\Services\MediaService\ApiClient;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Tv\Repositories\Interfaces\TvRepositoryInterface;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use App\Shared\Support\Watchlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WatchListServices implements WatchListServiceInterface
{
    public function __construct(protected MovieRepositoryInterface $movieRepository, protected TvRepositoryInterface $tvRepository, protected MediaApiClientInterface $mediaApiClient)
    {}

    /**
     * @inheritDoc
     */
    public function addToWatchList(User $user, int $mediaId, string $mediaType): void
    {
        if (str_contains(strtolower($mediaType), 'actor')) {
            $this->ensureActor($mediaId);
            $user->watchlist()->firstOrCreate([
                'media_id' => $mediaId,
                'media_type' => $mediaType,
            ]);

            Watchlist::reset();

            return;
        }

        $repository = str_contains(strtolower($mediaType), 'tv')
            ? $this->tvRepository
            : $this->movieRepository;

        $media = $repository->find($mediaId);

        $resource = null;

        $type = preg_match('/\\\\(\w+)$/', $mediaType, $m) ? strtolower($m[1]) : null;

        if (!$media) {
            $resource = $this->mediaApiClient->fetchMediaWithDetails($mediaId, $type, false);
        }

        DB::transaction(function () use ($user, $mediaId, $mediaType, $media, $resource, $repository) {
            $user->watchlist()->firstOrCreate([
                'media_id' => $mediaId,
                'media_type' => $mediaType,
            ]);

            if (!$media && $resource) {

                $repository->createRecord(Categories::POPULAR->value, collect([$resource]));
            }
        });

        Watchlist::reset();
    }


    /**
     * @inheritDoc
     */
    public function removeFromWatchList(User $user, int $mediaId, string $mediaType): void
    {
      $user->watchlist()->where('media_id', $mediaId)
          ->where('media_type', $mediaType)
          ->delete();

      Watchlist::reset();
    }

    /**
     * Make sure a minimal Actor row exists before it can be favourited, so the
     * morphTo relation on the watchlist can resolve it.
     */
    private function ensureActor(int $actorId): void
    {
        if (\App\Actor\Models\Actor::find($actorId)) {
            return;
        }

        $person = $this->mediaApiClient->fetchMediaWithDetails((string) $actorId, 'person', false);

        if (! empty($person['id'])) {
            \App\Actor\Models\Actor::upsert([[
                'id' => $person['id'],
                'name' => $person['name'] ?? 'Unknown',
                'profile_path' => $person['profile_path'] ?? null,
                'known_for' => $person['known_for_department'] ?? '',
                'popularity' => json_encode($person['popularity'] ?? 0),
                'created_at' => now(),
                'updated_at' => now(),
            ]], ['id'], ['name', 'profile_path', 'known_for', 'popularity']);
        }
    }

    /**
     * @inheritDoc
     */
    public function myWatchLists(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        return $user->watchlist()
            ->with('media')
            ->get()
            ->groupBy('media_type')
            ->map(function ($items, $type) {
                $mediaType = strtolower(class_basename($type));

                return [
                    $mediaType => $items->pluck('media')->filter()
                ];
            })
            ->collapse();
    }

}
