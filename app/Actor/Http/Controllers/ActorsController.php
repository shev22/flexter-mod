<?php

namespace App\Actor\Http\Controllers;


use App\Actor\Http\Request\ActorsFilterRequest;
use App\Http\Controllers\Controller;

class ActorsController extends Controller
{
    public function __invoke(ActorsFilterRequest $request): array
    {
        return [];
    }

}
