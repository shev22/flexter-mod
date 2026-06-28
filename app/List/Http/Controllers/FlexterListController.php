<?php

namespace App\List\Http\Controllers;

use App\Http\Controllers\Controller;
use App\List\Services\FlexterListService;
use Inertia\Inertia;
use Inertia\Response;

class FlexterListController extends Controller
{
    public function __construct(private readonly FlexterListService $lists) {}

    public function index(): Response
    {
        return Inertia::render('Lists/Index', [
            'lists' => $this->lists->allLists(),
        ]);
    }

    public function show(string $slug): Response
    {
        $list = $this->lists->show($slug, auth()->user());

        abort_if($list === null, 404);

        return Inertia::render('Lists/Show', [
            'list' => $list,
        ]);
    }
}
