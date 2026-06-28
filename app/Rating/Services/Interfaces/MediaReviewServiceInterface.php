<?php

namespace App\Rating\Services\Interfaces;

use App\Models\User;
use App\Rating\Models\MediaReview;

interface MediaReviewServiceInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function forMedia(User $user, string $mediaType, int $mediaId): ?array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function diary(User $user, int $limit = 50): array;

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function upsert(User $user, array $payload): array;

    public function destroy(User $user, MediaReview $review): void;
}
