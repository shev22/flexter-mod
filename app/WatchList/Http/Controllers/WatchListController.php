<?php

namespace App\WatchList\Http\Controllers;

use App\Http\Controllers\Controller;
use App\WatchList\Http\Request\WatchListFiltrationRequest;
use Inertia\Inertia;


class WatchListController extends Controller
{
    public function __invoke(WatchListFiltrationRequest $request): \Inertia\Response
    {
        return Inertia::render('Main/Watchlist');

    }
}
