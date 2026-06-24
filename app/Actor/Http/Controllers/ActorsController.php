<?php

namespace App\Actor\Http\Controllers;

use App\Actor\Http\Request\ActorsFilterRequest;
use App\Actor\Services\Interfaces\ActorServiceInterface;
use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Shared\Data\PersonDetailData;
use App\Shared\Data\PersonSummaryData;
use App\Shared\Support\Watchlist;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ActorsController extends Controller
{
    public function __construct(
        private readonly ActorServiceInterface $actorService,
        private readonly WatchListServiceInterface $watchListService,
    ) {}

    public function __invoke(ActorsFilterRequest $request): Response
    {
        $search = $request->filled('search') ? trim((string) $request->query('search')) : null;
        $sort = in_array($request->query('sort'), ['popularity', 'name'], true) ? $request->query('sort') : 'popularity';

        $actors = $this->actorService->getActors($search, $sort, 30);

        return Inertia::render('Actors/Index', [
            'actors' => [
                'data' => collect($actors->items())
                    ->map(fn ($actor) => PersonSummaryData::fromModel($actor, Watchlist::has('actor', (int) $actor->id)))
                    ->values()
                    ->all(),
                'current_page' => $actors->currentPage(),
                'last_page' => $actors->lastPage(),
                'total' => $actors->total(),
            ],
            'filters' => [
                'search' => $search,
                'sort' => $sort,
            ],
        ]);
    }

    public function show(string $slug, string $actorId): Response
    {
        $actor = $this->actorService->getActorWithAttachments($actorId);

        return Inertia::render('Actors/Show', [
            'actor' => PersonDetailData::fromTmdb($actor),
            'is_favorite' => Watchlist::has('actor', (int) ($actor['id'] ?? 0)),
        ]);
    }

    public function favorite(Request $request): void
    {
        $request->validate(['id' => 'required|integer']);

        /** @var User $user */
        $user = Auth::user();
        $this->watchListService->addToWatchList($user, (int) $request->input('id'), MediaType::PERSON->getMappedClass());
    }

    public function unfavorite(Request $request): void
    {
        $request->validate(['id' => 'required|integer']);

        /** @var User $user */
        $user = Auth::user();
        $this->watchListService->removeFromWatchList($user, (int) $request->input('id'), MediaType::PERSON->getMappedClass());
    }
}
