<?php

use App\Actor\Http\Controllers\ActorsController;
use App\DashBoard\Http\Controllers\DashBoardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Search\SearchController;
use App\Movie\Http\Controllers\MovieController;
use App\Tv\Http\Controllers\TvController;
use App\User\Http\Controllers\IndexController as UserIndexController;
use App\WatchList\Http\Controllers\WatchListController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request;

Route::middleware('auth')->group(function () {
    Route::get('admin', TvController::class)->name('admin');
    Route::get('watchlist', WatchListController::class)->name('watchlist');
    Route::post('watchlist/movie/add', [MovieController::class, 'addToWatchList'])->name('movie.add.watchlist');
    Route::post('watchlist/movie/remove', [MovieController::class, 'removeFromWatchList'])->name('movie.remove.watchlist');

    Route::post('watchlist/tv/add', [TvController::class, 'addToWatchList'])->name('tv.add.watchlist');
    Route::post('watchlist/tv/remove', [TvController::class, 'removeFromWatchList'])->name('tv.remove.watchlist');

    Route::get('dashboard', DashBoardController::class)->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::inertia('login', 'Auth/Login')->name('login');
    Route::inertia('register', 'Auth/Register')->name('register');
});

Route::get('/', HomeController::class)->name('home');
Route::get('actors', ActorsController::class)->name('actors');
Route::get('actors/{slug}/{id}', [ActorsController::class, 'show'])->name('actor.show');

Route::get('movies', MovieController::class)->name('movies');
Route::get('/movie/{slug}/{id}', [MovieController::class, 'show'])->name('movie.show');

Route::get('tv', TvController::class)->name('tv');
Route::get('tv/{slug}/{id}', [TvController::class, 'show'])->name('tv.show');

Route::get('search', [SearchController::class, 'show'])->name('search.show');
Route::get('api/search', SearchController::class)->name('api.search');
Route::get('feedback', FeedbackController::class)->name('feedback');



