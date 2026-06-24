<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Services\HomeService\Interfaces\HomeServiceInterface;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(protected HomeServiceInterface $homeService)
    {
    }

    public function __invoke(): Response
    {
        return Inertia::render('Home', $this->homeService->getHomePageData());
    }
}
