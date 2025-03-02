<?php

namespace App\TopRated\Http\Controllers;

use App\Http\Controllers\Controller;
use App\TopRated\Http\Request\TopRatedFiltrationRequest;

class TopRatedController extends Controller
{
    public function __invoke(TopRatedFiltrationRequest $request): array
    {
       return [];
    }
}
