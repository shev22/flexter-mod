<?php

namespace App\Stats\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Stats\Services\StatsService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StatsController extends Controller
{
    public function __construct(private readonly StatsService $stats) {}

    public function __invoke(): Response
    {
        return Inertia::render('Stats', [
            'profile' => $this->stats->profile(Auth::user()),
        ]);
    }
}
