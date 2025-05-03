<?php

namespace App\Http\Controllers\Home;

use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Movie\Resources\MovieResource;
use App\Movie\Services\Interfaces\MovieServiceInterface;
use App\Services\HomeService\HomeService;
use App\Services\MediaService\ApiClient;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use Inertia\Inertia;
use Inertia\Response;


class HomeController extends Controller
{
    public function __construct(protected HomeService $homeService, protected MediaApiClientInterface $movieService)
    {
    }
   public function __invoke():Response
   {
    //   dd($this->movieService->fetchMediaDetails(22, MediaType::MOVIE->getLabel()));

       $data = $this->homeService->getHomePageData();
       return Inertia::render('Main/Home', ['movies' => $data]);
   }
}

