<?php

namespace App\Movie\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Movie\Http\Request\MoviesFilterRequest;

class MovieController extends Controller
{
   public function __invoke(MoviesFilterRequest $request): array
   {
      return [];
   }
}
