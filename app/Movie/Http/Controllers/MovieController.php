<?php

namespace App\Movie\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Movie\Http\Request\MoviesFilterRequest;
use App\Movie\Services\Interfaces\MovieServiceInterface;
use App\Movie\Services\MovieService;
use Inertia\Inertia;
use Inertia\Response;

class MovieController extends Controller
{
    public function __construct(protected MovieServiceInterface $movieService)
    {
    }

    public function __invoke(MoviesFilterRequest $request): Response
   {
       $this->movieService->popularMovies();
       return Inertia::render('Main/Movies');
   }
}
