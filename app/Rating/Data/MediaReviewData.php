<?php

namespace App\Rating\Data;

use App\Rating\Models\MediaReview;
use App\Shared\Data\MediaCardData;
use App\Shared\Support\Watchlist;

final class MediaReviewData
{
    public function __construct(
        public int $id,
        public ?int $rating,
        public ?string $body,
        public ?string $watchedOn,
        public ?array $media,
    ) {}

    public static function fromModel(MediaReview $review): self
    {
        $mediaModel = $review->resolveMedia();
        $type = $review->media_type === 'tv' ? 'tv' : 'movie';

        return new self(
            id: $review->id,
            rating: $review->rating,
            body: $review->body,
            watchedOn: $review->watched_on?->toDateString(),
            media: $mediaModel
                ? MediaCardData::fromModel($mediaModel, Watchlist::has($type, (int) $mediaModel->id))->toArray()
                : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'body' => $this->body,
            'watched_on' => $this->watchedOn,
            'media' => $this->media,
        ];
    }
}
