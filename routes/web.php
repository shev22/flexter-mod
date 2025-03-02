<?php

use App\Actor\Http\Controllers\ActorsController;
use App\DashBoard\Http\Controllers\DashBoardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Search\SearchController;
use App\Movie\Http\Controllers\MovieController;
use App\Series\Http\Controllers\SeriesController;
use App\WatchList\Http\Controllers\WatchListController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/admin', SeriesController::class)->name('admin');
    Route::get('/watchlist', WatchListController::class)->name('watchlist');
    Route::get('/dashboard', DashBoardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::inertia('/register', 'Auth/Register')->name('register');
});

Route::get('/', HomeController::class)->name('home');
Route::get('/actors', ActorsController::class)->name('actors');
Route::get('/movies', MovieController::class)->name('movies');
Route::get('/series', SeriesController::class)->name('series');

Route::get('/search', SearchController::class)->name('search');
Route::get('/feedback', FeedbackController::class)->name('feedback');



