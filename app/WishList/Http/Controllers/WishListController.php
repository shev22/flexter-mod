<?php

namespace App\WishList\Http\Controllers;

use App\Http\Controllers\Controller;
use App\WishList\Http\Request\WishListFiltrationRequest;

class WishListController extends Controller
{
    public function __invoke(WishListFiltrationRequest $request): array
    {
        return [];
    }
}
