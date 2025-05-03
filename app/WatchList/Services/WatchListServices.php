<?php

namespace App\WatchList\Services;

use App\Enums\Categories;
use App\Models\User;
use App\Movie\Models\Movie;
use App\Movie\Repositories\Interfaces\MovieRepositoryInterface;
use App\Services\MediaService\ApiClient;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Tv\Repositories\Interfaces\TvRepositoryInterface;
use App\WatchList\Models\WatchList;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
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
    }


    /**
     * @inheritDoc
     */
    public function removeFromWatchList(User $user, int $mediaId, string $mediaType): void
    {
      $user->watchlist()->where('media_id', $mediaId)
          ->where('media_type', $mediaType)
          ->delete() ;
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
