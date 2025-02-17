<?php

namespace App\Actor\Http\Controllers;


use App\Actor\Http\Request\ActorsFilterRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Actor\Repositories\Interfaces\ActorsRepositoryInterface;
use App\Http\Controllers\Controller;

class ActorsController extends Controller
{
    public function __construct(private readonly ActorsRepositoryInterface $actorsRepository)
    {
    }

    public function __invoke(ActorsFilterRequest $request): LengthAwarePaginator
    {
        try {
            $search = $request->get('search');
            $page = $request->get('page', 1);
            $perPage = $request->get('perPage', 10);

            return $this->actorsRepository->actors($search, $page, $perPage);
        }catch (\Exception $exception){
            echo $exception;
        }
    }

}
