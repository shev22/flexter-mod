<?php

namespace App\List\Services;

use App\List\Models\FlexterList;
use App\List\Support\ListIcons;
use App\Models\User;
use App\Movie\Models\Movie;
use App\Shared\Data\MediaCardData;
use App\Shared\Support\AppCache;
use App\Shared\Support\Watchlist;
use App\Tv\Models\Tv;

class FlexterListService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function featured(int $limit = 6): array
    {
        $lists = AppCache::lists(
            "featured.{$limit}",
            fn () => FlexterList::query()
                ->whereNull('user_id')
                ->where('is_featured', true)
                ->withCount('items')
                ->orderBy('sort_order')
                ->limit($limit)
                ->with(['items' => fn ($q) => $q->orderBy('sort_order')])
                ->get(),
        );

        return $lists
            ->map(fn (FlexterList $list) => $this->presentList($list, $list->items, (int) $list->items_count))
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function allLists(): array
    {
        $lists = AppCache::lists(
            'index',
            fn () => FlexterList::query()
                ->whereNull('user_id')
                ->withCount('items')
                ->orderBy('sort_order')
                ->get(),
        );

        return $lists
            ->map(fn (FlexterList $list) => [
                'id' => $list->id,
                'title' => $list->title,
                'slug' => $list->slug,
                'description' => $list->description,
                'icon' => ListIcons::normalize($list->icon),
                'item_count' => $list->items_count,
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function show(string $slug, ?User $viewer = null): ?array
    {
        $list = AppCache::lists(
            "show.{$slug}",
            fn () => FlexterList::query()
                ->where('slug', $slug)
                ->withCount('items')
                ->with(['items' => fn ($q) => $q->orderBy('sort_order')])
                ->first(),
        );

        if (! $list instanceof FlexterList) {
            return null;
        }

        if ($list->user_id !== null) {
            $isOwner = $viewer !== null && $list->user_id === $viewer->id;

            if (! $isOwner && $list->visibility !== 'public') {
                return null;
            }
        }

        $presented = $this->presentList($list, $list->items, (int) $list->items_count);
        $presented['is_owner'] = $viewer !== null && $list->user_id === $viewer->id;
        $presented['visibility'] = $list->visibility ?? 'private';
        $presented['is_mine'] = $list->user_id !== null;

        return $presented;
    }

    /**
     * @param  iterable<int, \App\List\Models\FlexterListItem>  $items
     * @return array<string, mixed>
     */
    private function presentList(FlexterList $list, iterable $items, ?int $itemCount = null): array
    {
        $movieIds = [];
        $tvIds = [];

        foreach ($items as $item) {
            if ($item->media_type === 'tv') {
                $tvIds[] = (int) $item->media_id;
            } else {
                $movieIds[] = (int) $item->media_id;
            }
        }

        $movies = $movieIds !== []
            ? Movie::query()->whereIn('id', array_unique($movieIds))->get()->keyBy('id')
            : collect();
        $tvs = $tvIds !== []
            ? Tv::query()->whereIn('id', array_unique($tvIds))->get()->keyBy('id')
            : collect();

        Watchlist::keys();

        $resolved = collect($items)
            ->map(function ($item) use ($movies, $tvs) {
                $media = $item->media_type === 'tv'
                    ? $tvs->get($item->media_id)
                    : $movies->get($item->media_id);

                if (! $media) {
                    return null;
                }

                $type = $item->media_type === 'tv' ? 'tv' : 'movie';

                return MediaCardData::fromModel($media, Watchlist::has($type, (int) $media->id))->toArray();
            })
            ->filter()
            ->values()
            ->all();

        return [
            'id' => $list->id,
            'title' => $list->title,
            'slug' => $list->slug,
            'description' => $list->description,
            'icon' => ListIcons::normalize($list->icon),
            'item_count' => $itemCount ?? count($resolved),
            'items' => $resolved,
        ];
    }
}
