<?php

namespace App\Movie\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Movie\Http\Request\MoviesFilterRequest;
use Inertia\Inertia;
use Inertia\Response;

class MovieController extends Controller
{
   public function __invoke(MoviesFilterRequest $request): Response
   {
       return Inertia::render('Main/Movies');
   }
}
