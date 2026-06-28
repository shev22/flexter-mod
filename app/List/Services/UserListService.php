<?php

namespace App\List\Services;

use App\List\Models\FlexterList;
use App\List\Models\FlexterListItem;
use App\List\Support\ListIcons;
use App\Models\User;
use App\Movie\Models\Movie;
use App\Shared\Data\MediaCardData;
use App\Shared\Support\AppCache;
use App\Shared\Support\Watchlist;
use App\Tv\Models\Tv;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserListService
{
    public function __construct(
        private readonly FlexterListService $lists,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function mine(User $user): array
    {
        return FlexterList::query()
            ->where('user_id', $user->id)
            ->withCount('items')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (FlexterList $list) => [
                'id' => $list->id,
                'title' => $list->title,
                'slug' => $list->slug,
                'description' => $list->description,
                'visibility' => $list->visibility,
                'icon' => ListIcons::normalize($list->icon),
                'item_count' => (int) $list->items_count,
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function create(User $user, array $payload): array
    {
        $title = trim((string) ($payload['title'] ?? ''));

        if ($title === '') {
            throw ValidationException::withMessages(['title' => 'A list title is required.']);
        }

        $slug = $this->uniqueSlug($title);

        $list = FlexterList::query()->create([
            'user_id' => $user->id,
            'title' => $title,
            'slug' => $slug,
            'description' => $payload['description'] ?? null,
            'icon' => ListIcons::normalize($payload['icon'] ?? 'bookmark'),
            'visibility' => in_array($payload['visibility'] ?? 'private', ['public', 'private'], true)
                ? $payload['visibility']
                : 'private',
            'is_featured' => false,
            'sort_order' => 0,
        ]);

        AppCache::bustLists();

        return $this->lists->show($list->slug, $user) ?? [
            'id' => $list->id,
            'title' => $list->title,
            'slug' => $list->slug,
            'description' => $list->description,
            'icon' => ListIcons::normalize($list->icon),
            'item_count' => 0,
            'items' => [],
            'is_owner' => true,
            'visibility' => $list->visibility,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function show(User $user, FlexterList $list): ?array
    {
        if ($list->user_id !== $user->id) {
            return null;
        }

        $presented = $this->lists->show($list->slug, $user);

        if ($presented === null) {
            return null;
        }

        $presented['is_owner'] = true;
        $presented['visibility'] = $list->visibility;

        return $presented;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function addItem(User $user, FlexterList $list, string $mediaType, int $mediaId): array
    {
        $this->assertOwner($user, $list);

        if (! in_array($mediaType, ['movie', 'tv'], true)) {
            throw ValidationException::withMessages(['media_type' => 'Invalid media type.']);
        }

        $exists = FlexterListItem::query()
            ->where('flexter_list_id', $list->id)
            ->where('media_type', $mediaType)
            ->where('media_id', $mediaId)
            ->exists();

        if (! $exists) {
            $position = (int) FlexterListItem::query()->where('flexter_list_id', $list->id)->max('sort_order');

            FlexterListItem::query()->create([
                'flexter_list_id' => $list->id,
                'media_type' => $mediaType,
                'media_id' => $mediaId,
                'sort_order' => $position + 1,
            ]);

            AppCache::bustLists();
        }

        return $this->show($user, $list->fresh())['items'] ?? [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function removeItem(User $user, FlexterList $list, string $mediaType, int $mediaId): array
    {
        $this->assertOwner($user, $list);

        FlexterListItem::query()
            ->where('flexter_list_id', $list->id)
            ->where('media_type', $mediaType)
            ->where('media_id', $mediaId)
            ->delete();

        AppCache::bustLists();

        return $this->show($user, $list->fresh())['items'] ?? [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function optionsForMedia(User $user, string $mediaType, int $mediaId): array
    {
        return FlexterList::query()
            ->where('user_id', $user->id)
            ->with(['items' => fn ($q) => $q->where('media_type', $mediaType)->where('media_id', $mediaId)])
            ->orderBy('title')
            ->get()
            ->map(fn (FlexterList $list) => [
                'id' => $list->id,
                'title' => $list->title,
                'slug' => $list->slug,
                'contains' => $list->items->isNotEmpty(),
            ])
            ->all();
    }

    private function assertOwner(User $user, FlexterList $list): void
    {
        abort_unless($list->user_id === $user->id, 403);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base !== '' ? $base : 'list';
        $candidate = $slug;
        $suffix = 1;

        while (FlexterList::query()->where('slug', $candidate)->exists()) {
            $candidate = "{$slug}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
