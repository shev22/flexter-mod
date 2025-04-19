<?php

namespace App\Actor\Http\Controllers;


use App\Actor\Http\Request\ActorsFilterRequest;
use App\Actor\Services\Interfaces\ActorServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Actor\Repositories\Interfaces\ActorRepositoryInterface;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

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
            $perPage = $request->get('perPage', 300);

           $actors = $this->actorService->getActors($search, $page, $perPage);
            return Inertia::render('Main/Actors', ['actors' => $actors]);
        } catch (\Exception $exception) {

            return Inertia::render('ErrorPage', ['message' => $exception->getMessage()]);
        }
    }
}
