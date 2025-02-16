<?php

namespace App\Series\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Series\Http\Request\SeriesFilterationRequest;

class SeriesController extends Controller
{
    public function __invoke(SeriesFilterationRequest $request): array
    {
        return [];
    }
}
