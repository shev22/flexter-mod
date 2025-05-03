<?php

namespace App\DashBoard\Http\Controllers;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashBoardController extends Controller
{
public function __invoke(): Response
{
   return Inertia::render('User/Dashboard');
}
}
