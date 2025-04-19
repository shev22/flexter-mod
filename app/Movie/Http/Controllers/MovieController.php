<?php

namespace App\Movie\Http\Controllers;

use App\Actor\Services\ActorService;
use App\Actor\Services\Interfaces\ActorServiceInterface;
use App\Enums\MediaType;
use App\Genre\Services\GenreService;
use App\Genre\Services\Interfaces\GenreServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Movie\Http\Request\MoviesFilterRequest;
use App\Movie\Repositories\Interfaces\MovieRepositoryInterface;
use App\Movie\Resources\MovieResource;
use App\Movie\Services\Interfaces\MovieServiceInterface;
use App\Movie\Services\MovieService;
use App\WatchList\Http\Request\WatchListFiltrationRequest;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MovieController extends Controller
{
    public function __construct(protected MovieServiceInterface $movieService, private readonly WatchListServiceInterface $watchListService, protected MovieRepositoryInterface $genreService)
    {}

    public function __invoke(MoviesFilterRequest $request)
   {


       $movies = $this->movieService->getMovies();
       return Inertia::render('Main/Movies', ['movies' => MovieResource::collection($movies)]);
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
        $type = MediaType::MOVIE->getMappedClass();
        $this->watchListService->addToWatchList($user, $id, $type);
    }

}
