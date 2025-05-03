<?php

namespace App\Tv\Http\Controllers;

use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Movie\Resources\MovieResource;
use App\Movie\Resources\ShowMovieResource;
use App\Movie\Services\Interfaces\MovieServiceInterface;
use App\Tv\Http\Request\TvFilterationRequest;
use App\Tv\Resource\ShowTvResource;
use App\Tv\Resource\TvResource;
use App\Tv\Services\Interfaces\TvServiceInterface;
use App\WatchList\Http\Request\WatchListFiltrationRequest;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TvController extends Controller
{
    public function __construct(protected TvServiceInterface $tvService, private readonly WatchListServiceInterface $watchListService)
    {}

    public function __invoke(TvFilterationRequest $request): Response
    {
        $tv = $this->tvService->getTv();
        return Inertia::render('Main/Tv/Tv', [
            'tv' => TvResource::collection($tv),
            'currentPage' => $tv->currentPage(),
            'lastPage' => $tv->lastPage(),
        ]);
    }

    public function show(string $slug, string $tvId): Response
    {
        $tv = $this->tvService->getTvWithRelatedTv($tvId);

        return Inertia::render('Main/Tv/Show', [
            'tv' => ShowTvResource::make($tv),
        ]);
    }

    /**
     * @param WatchListFiltrationRequest $request
     * @return void
     */
    public function addToWatchList(WatchListFiltrationRequest $request):void
    {
        $id = $request->input('id');

        /**
         * @var User $user
         */
        $user = Auth::user();
        $type = MediaType::TV->getMappedClass();
        $this->watchListService->addToWatchList($user, $id, $type);
    }

    /**
     * @param WatchListFiltrationRequest $request
     * @return void
     */
    public function removeFromWatchList(WatchListFiltrationRequest $request):void
    {
        $id = $request->input('id');

        /**
         * @var User $user
         */
        $user = Auth::user();
        $type = MediaType::TV->getMappedClass();

        $this->watchListService->removeFromWatchList($user, $id, $type);
    }
}
