<?php

namespace App\Series\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Series\Http\Request\SeriesFilterationRequest;
use Inertia\Inertia;
use Inertia\Response;

class SeriesController extends Controller
{
    public function __invoke(SeriesFilterationRequest $request): Response
    {
        return Inertia::render('Main/Series');
    }
}
