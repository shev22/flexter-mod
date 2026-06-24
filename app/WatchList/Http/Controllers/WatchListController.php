<?php

namespace App\WatchList\Http\Controllers;

use App\Enums\Categories;
use App\Http\Controllers\Controller;
use App\Movie\Models\Movie;
use App\Shared\Data\PersonSummaryData;
use App\Shared\Support\Present;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WatchListController extends Controller
{
    private const PER_PAGE = 24;

    public function __construct(private readonly WatchListServiceInterface $watchListService) {}

    public function __invoke(Request $request): Response
    {
        $lists = $this->watchListService->myWatchLists();
        $sort = (string) $request->query('sort', 'added');
        $page = max(1, (int) $request->query('page', 1));

        $movies = $this->paginateCollection(collect($lists['movie'] ?? collect()), $page);
        $shows = $this->paginateCollection(collect($lists['tv'] ?? collect()), $page);
        $actors = $this->paginateCollection(collect($lists['actor'] ?? collect()), $page);

        return Inertia::render('Watchlist', [
            'movies' => [
                'data' => Present::cardList($this->sortMedia(collect($movies['items']), $sort)),
                'current_page' => $movies['current_page'],
                'last_page' => $movies['last_page'],
                'total' => $movies['total'],
            ],
            'shows' => [
                'data' => Present::cardList($this->sortMedia(collect($shows['items']), $sort)),
                'current_page' => $shows['current_page'],
                'last_page' => $shows['last_page'],
                'total' => $shows['total'],
            ],
            'actors' => [
                'data' => collect($actors['items'])
                    ->map(fn ($actor) => PersonSummaryData::fromModel($actor, true))
                    ->values()
                    ->all(),
                'current_page' => $actors['current_page'],
                'last_page' => $actors['last_page'],
                'total' => $actors['total'],
            ],
            'suggestions' => Present::cardList(
                Movie::query()
                    ->select(['id', 'title', 'poster_path', 'release_date', 'vote_average', 'genre_ids', 'overview'])
                    ->where('category', Categories::POPULAR->value)
                    ->orderByRaw('CAST(popularity AS DECIMAL(12,3)) DESC')
                    ->limit(20)
                    ->get()
            ),
        ]);
    }

    /** @return array{items: array, current_page: int, last_page: int, total: int} */
    private function paginateCollection($collection, int $page): array
    {
        $total = $collection->count();
        $lastPage = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * self::PER_PAGE;

        return [
            'items' => $collection->slice($offset, self::PER_PAGE)->values()->all(),
            'current_page' => $page,
            'last_page' => $lastPage,
            'total' => $total,
        ];
    }

    private function sortMedia($collection, string $sort)
    {
        return match ($sort) {
            'rating' => $collection->sortByDesc(fn ($m) => (float) ($m->vote_average ?? 0))->values(),
            'title' => $collection->sortBy(fn ($m) => $m->title ?? '')->values(),
            'year' => $collection->sortByDesc(fn ($m) => $m->release_date ?? '')->values(),
            default => $collection->sortByDesc(fn ($m) => $m->created_at ?? now())->values(),
        };
    }
}
