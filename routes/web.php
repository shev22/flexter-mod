<?php

use App\Actor\Http\Controllers\ActorsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/actors', ActorsController::class)->name('actors');
