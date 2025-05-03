<?php

namespace App\Services\MediaService\Interfaces;

use Illuminate\Support\Collection;

interface MediaApiClientInterface
{
    /**
     * @param string $mediaType
     * @param string $category
     * @param int $pages
     * @return Collection
     */
    public function fetchMedia(string $mediaType, string $category, int $pages): Collection;

    /**
     * @return Collection
     */
    public function fetchGenre(): Collection;

    /**
     * @param string $mediaId
     * @param string $mediaType
     * @return array
     */
    public function fetchMediaWithDetails(string $mediaId, string $mediaType):array;

    /**
     * @param string $mediaId
     * @param string $mediaType
     * @return array
     */
    public function fetchRelatedMedia(string $mediaId, string $mediaType):array;

    /**
     * @param string $mediaType
     * @param string $period
     * @return array
     */
    public function fetchTrending(string $mediaType, string $period):array;

    /**
     * @param string $query
     * @return collection
     */
    public function search(string $query):collection;
}
