<?php

namespace App\Tv\Http\Controllers;

use App\Comment\Services\Interfaces\CommentServiceInterface;
use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Shared\Data\MediaCardData;
use App\Shared\Data\MediaDetailData;
use App\Shared\Data\MediaFilterData;
use App\Shared\Support\Present;
use App\Shared\Support\Watchlist;
use App\WatchHistory\Services\Interfaces\WatchHistoryServiceInterface;
use App\Tv\Http\Request\TvFilterationRequest;
use App\Tv\Services\Interfaces\TvServiceInterface;
use App\WatchList\Http\Request\WatchListFiltrationRequest;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TvController extends Controller
{
    public function __construct(
        protected TvServiceInterface $tvService,
        private readonly WatchListServiceInterface $watchListService,
        private readonly WatchHistoryServiceInterface $watchHistoryService,
        private readonly CommentServiceInterface $commentService,
    ) {}

    public function __invoke(TvFilterationRequest $request): Response
    {
        $filter = MediaFilterData::fromRequest($request);

        return Inertia::render('Tv/Index', [
            'shows' => Present::paginated($this->tvService->getTv($filter)),
            'filters' => $filter,
        ]);
    }

    public function show(string $slug, string $tvId): Response
    {
        $detail = $this->tvService->getTvWithRelatedTv($tvId);

        $related = collect($detail['related'] ?? [])
            ->take(12)
            ->map(fn ($item) => MediaCardData::fromTmdb($item, 'tv', Watchlist::has('tv', (int) ($item['id'] ?? 0)))->toArray())
            ->values()
            ->all();

        $watchProgress = null;
        $watchContext = null;
        if ($user = Auth::user()) {
            $entry = $this->watchHistoryService->latestProgressFor($user, 'tv', (int) $tvId);
            $watchProgress = $entry ? (int) $entry->progress_percent : 0;
            $watchContext = $entry ? [
                'season' => $entry->season_number,
                'episode' => $entry->episode_number,
            ] : null;
        }

        $media = MediaDetailData::fromTmdb($detail, 'tv', $related, Watchlist::has('tv', (int) ($detail['id'] ?? 0)));

        return Inertia::render('Tv/Show', [
            'media' => $media->toArray(),
            'watchProgress' => $watchProgress,
            'watchContext' => $watchContext,
            'comments' => $this->commentService->forMedia('tv', (int) $tvId, $user),
        ]);
    }

    public function addToWatchList(WatchListFiltrationRequest $request): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->watchListService->addToWatchList($user, (int) $request->input('id'), MediaType::TV->getMappedClass());
    }

    public function removeFromWatchList(WatchListFiltrationRequest $request): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->watchListService->removeFromWatchList($user, (int) $request->input('id'), MediaType::TV->getMappedClass());
    }
}
