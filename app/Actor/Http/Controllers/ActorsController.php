<?php

namespace App\Actor\Http\Controllers;


use App\Actor\Http\Request\ActorsFilterRequest;
use App\Actor\Resource\ActorResource;
use App\Actor\Resource\ShowActorResource;
use App\Actor\Services\Interfaces\ActorServiceInterface;
use App\Movie\Resources\MovieResource;
use App\Movie\Resources\ShowMovieResource;
use App\Tv\Resource\TvResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Actor\Repositories\Interfaces\ActorRepositoryInterface;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ActorsController extends Controller
{
    public function __construct(private readonly ActorServiceInterface $actorService)
    {
    }

    public function __invoke(ActorsFilterRequest $request): \Inertia\Response
    {
        try {
            $search = $request->get('search');
            $page = $request->get('page', 1);
            $perPage = $request->get('perPage', 25);

           $actors = $this->actorService->getActors($search, $page, $perPage);

            return Inertia::render('Main/Actors/Actors', [
                'actors' => ActorResource::collection($actors),
                'currentPage' => $actors->currentPage(),
                'lastPage' => $actors->lastPage(),
            ]);
        } catch (\Exception $exception) {

            return Inertia::render('ErrorPage', ['message' => $exception->getMessage()]);
        }
    }

    public function show(string $slug, string $actorId): Response
    {
        $actor = $this->actorService->getActorWithAttachments($actorId);

        return Inertia::render('Main/Actors/Show', [
            'actor' => ShowActorResource::make($actor),
            'images' => collect($actor['images']['profiles'])->pluck('file_path'),
            "tv" => TvResource::collection($actor['tv_credits']['cast']),
            "movies" => MovieResource::collection($actor['movie_credits']['cast']),
        ]);
    }
}
