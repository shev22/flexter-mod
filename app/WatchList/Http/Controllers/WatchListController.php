<?php

namespace App\WatchList\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Movie\Resources\MovieResource;
use App\WatchList\Http\Request\WatchListFiltrationRequest;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class WatchListController extends Controller
{
    public function __construct(private readonly WatchListServiceInterface $watchListService)
    {
    }

    public function __invoke(): \Inertia\Response
    {
        $userWatchLists = $this->watchListService->myWatchLists();

        return Inertia::render('Main/Watchlist', ['myWatchLists' => MovieResource::collection($userWatchLists)]);
    }

}
