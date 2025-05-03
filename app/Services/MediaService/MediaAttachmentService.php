<?php

namespace App\Services\MediaService;

use App\Enums\Categories;
use App\Enums\MediaType;
use App\Movie\Repositories\Interfaces\MovieRepositoryInterface;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use App\Services\MediaService\Interfaces\MediaAttachmentServiceInterface;
use App\Tv\Repositories\Interfaces\TvRepositoryInterface;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Http;

class MediaAttachmentService implements MediaAttachmentServiceInterface
{
    public function __construct(protected  MediaApiClientInterface $mediaApiClient, protected MovieRepositoryInterface $movieRepository, protected TvRepositoryInterface $tvRepository)
    {}

    /**
     * @param array $media
     * @param  string $mediaType
     * @return array[]
     */
    private function getMediaAttachments(array $media, string $mediaType): array
    {
        $resourse = $this->mediaApiClient->fetchMediaWithDetails($media['id'], $mediaType);

        if (!empty($resourse['images']['logos'])) {
            foreach ($resourse['images']['logos'] as $item) {
                if (!empty($item['iso_639_1']) && $item['iso_639_1'] === 'en') {
                    $media['logo'] = $item['file_path'];
                    break;
                }
            }
        }

        if (!empty($resourse['videos']['results'])) {
            foreach ($resourse['videos']['results'] as $item) {
                if ($item['type'] === 'Trailer') {
                    $media['trailer'] = $item['key'];
                    break;
                }
            }
        }

        return $media;
    }

    /**
     * @inheritDoc
     */
    public function setMediaAttachments(array $mediaCollection, string $mediaType): void
    {
        $results = [];

        $repository = str_contains(strtolower($mediaType), 'tv')
            ? $this->tvRepository
            : $this->movieRepository;

        foreach ($mediaCollection as $media) {
            $results[] = $this->getMediaAttachments($media, $mediaType);
        }

        $repository->createRecord(Categories::TRENDING->value, collect($results));
    }
}
