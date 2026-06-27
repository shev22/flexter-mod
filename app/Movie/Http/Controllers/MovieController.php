<?php

namespace App\Movie\Http\Controllers;

use App\Comment\Services\Interfaces\CommentServiceInterface;
use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Movie\Http\Request\MoviesFilterRequest;
use App\Movie\Services\Interfaces\MovieServiceInterface;
use App\Shared\Data\MediaDetailData;
use App\Shared\Data\MediaCardData;
use App\Shared\Data\MediaFilterData;
use App\Shared\Support\AdultContent;
use App\Shared\Support\Present;
use App\Shared\Support\Watchlist;
use App\WatchHistory\Services\Interfaces\WatchHistoryServiceInterface;
use App\WatchList\Http\Request\WatchListFiltrationRequest;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MovieController extends Controller
{
    public function __construct(
        protected MovieServiceInterface $movieService,
        private readonly WatchListServiceInterface $watchListService,
        private readonly WatchHistoryServiceInterface $watchHistoryService,
        private readonly CommentServiceInterface $commentService,
    ) {}

    public function __invoke(MoviesFilterRequest $request): Response
    {
        $filter = MediaFilterData::fromRequest($request);

        return Inertia::render('Movies/Index', [
            'movies' => Present::paginated($this->movieService->getMovies($filter)),
            'filters' => $filter,
        ]);
    }

    public function show(string $slug, string $movieId): Response
    {
        $detail = $this->movieService->getMovieWithRelatedMovies($movieId);

        if ($detail === [] || ! AdultContent::allowsDetail($detail)) {
            abort(404);
        }

        $related = collect(AdultContent::filterTmdb($detail['related'] ?? []))
            ->take(12)
            ->map(fn ($item) => MediaCardData::fromTmdb($item, 'movie', Watchlist::has('movie', (int) ($item['id'] ?? 0)))->toArray())
            ->values()
            ->all();

        $watchProgress = null;
        if ($user = Auth::user()) {
            $entry = $this->watchHistoryService->progressFor($user, 'movie', (int) $movieId);
            $watchProgress = $entry ? (int) $entry->progress_percent : 0;
        }

        $media = MediaDetailData::fromTmdb($detail, 'movie', $related, Watchlist::has('movie', (int) ($detail['id'] ?? 0)));

        return Inertia::render('Movies/Show', [
            'media' => $media->toArray(),
            'watchProgress' => $watchProgress,
            'comments' => $this->commentService->forMedia('movie', (int) $movieId, $user),
        ]);
    }

    public function addToWatchList(WatchListFiltrationRequest $request): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->watchListService->addToWatchList($user, (int) $request->input('id'), MediaType::MOVIE->getMappedClass());
    }

    public function removeFromWatchList(WatchListFiltrationRequest $request): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->watchListService->removeFromWatchList($user, (int) $request->input('id'), MediaType::MOVIE->getMappedClass());
    }
}
