<?php

namespace App\Rating\Services;

use App\Models\User;
use App\Rating\Data\MediaReviewData;
use App\Rating\Models\MediaReview;
use App\Rating\Services\Interfaces\MediaReviewServiceInterface;

class MediaReviewService implements MediaReviewServiceInterface
{
    public function forMedia(User $user, string $mediaType, int $mediaId): ?array
    {
        $review = MediaReview::query()
            ->where('user_id', $user->id)
            ->where('media_type', $mediaType)
            ->where('media_id', $mediaId)
            ->first();

        return $review ? MediaReviewData::fromModel($review)->toArray() : null;
    }

    public function diary(User $user, int $limit = 50): array
    {
        return MediaReview::query()
            ->where('user_id', $user->id)
            ->orderByDesc('watched_on')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (MediaReview $review) => MediaReviewData::fromModel($review)->toArray())
            ->all();
    }

    public function upsert(User $user, array $payload): array
    {
        $mediaType = (string) ($payload['media_type'] ?? '');
        $mediaId = (int) ($payload['media_id'] ?? 0);

        $review = MediaReview::query()->firstOrNew([
            'user_id' => $user->id,
            'media_type' => $mediaType,
            'media_id' => $mediaId,
        ]);

        if (array_key_exists('rating', $payload)) {
            $rating = $payload['rating'];

            if ($rating === null || $rating === '') {
                $review->rating = null;
            } else {
                $review->rating = max(1, min(10, (int) $rating));
            }
        }

        if (array_key_exists('body', $payload)) {
            $body = $payload['body'];
            $review->body = is_string($body) && trim($body) !== '' ? trim($body) : null;
        }

        if (array_key_exists('watched_on', $payload)) {
            $watchedOn = $payload['watched_on'];
            $review->watched_on = is_string($watchedOn) && $watchedOn !== '' ? $watchedOn : null;
        }

        $review->save();

        return MediaReviewData::fromModel($review->fresh())->toArray();
    }

    public function destroy(User $user, MediaReview $review): void
    {
        abort_unless($review->user_id === $user->id, 403);

        $review->delete();
    }
}
