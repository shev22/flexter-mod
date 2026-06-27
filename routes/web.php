<?php

use App\Actor\Http\Controllers\ActorsController;
use App\Billing\Http\Controllers\BillingController;
use App\Billing\Http\Controllers\StripeWebhookController;
use App\Comment\Http\Controllers\CommentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Search\SearchController;
use App\List\Http\Controllers\FlexterListController;
use App\Movie\Http\Controllers\MovieController;
use App\Settings\Http\Controllers\SettingsController;
use App\Stats\Http\Controllers\StatsController;
use App\Tv\Http\Controllers\TvController;
use App\WatchHistory\Http\Controllers\WatchHistoryController;
use App\WatchList\Http\Controllers\WatchListBulkController;
use App\WatchList\Http\Controllers\WatchListController;
use Illuminate\Support\Facades\Route;

/*
| Public browsing
*/
Route::get('/', HomeController::class)->name('home');

Route::get('movies', MovieController::class)->name('movies');
Route::get('movie/{slug}/{id}', [MovieController::class, 'show'])->name('movie.show');

Route::get('tv', TvController::class)->name('tv');
Route::get('tv/{slug}/{id}', [TvController::class, 'show'])->name('tv.show');

Route::get('actors', ActorsController::class)->name('actors');
Route::get('actors/{slug}/{id}', [ActorsController::class, 'show'])->name('actor.show');

Route::get('search', [SearchController::class, 'show'])->name('search.show');
Route::middleware('throttle:search')->group(function () {
    Route::get('api/search', SearchController::class)->name('api.search');
});

Route::get('feedback', [FeedbackController::class, 'show'])->name('feedback');
Route::post('feedback', [FeedbackController::class, 'store'])
    ->middleware('throttle:feedback')
    ->name('feedback.store');

Route::inertia('help', 'Main/Help')->name('help');

Route::post('stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('cashier.webhook');

Route::get('lists', [FlexterListController::class, 'index'])->name('lists');
Route::get('lists/{slug}', [FlexterListController::class, 'show'])->name('lists.show');

Route::get('subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');

/*
| Guest authentication
*/
Route::middleware('guest')->group(function () {
    Route::inertia('login', 'Auth/Login')->name('login');
    Route::inertia('register', 'Auth/Register')->name('register');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

/*
| Authenticated members
*/
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('watchlist', WatchListController::class)->name('watchlist');
    Route::delete('watchlist/bulk', [WatchListBulkController::class, 'destroy'])->name('watchlist.bulk');
    Route::post('watchlist/movie/add', [MovieController::class, 'addToWatchList'])->name('movie.add.watchlist');
    Route::post('watchlist/movie/remove', [MovieController::class, 'removeFromWatchList'])->name('movie.remove.watchlist');
    Route::post('watchlist/tv/add', [TvController::class, 'addToWatchList'])->name('tv.add.watchlist');
    Route::post('watchlist/tv/remove', [TvController::class, 'removeFromWatchList'])->name('tv.remove.watchlist');
    Route::post('favorites/actor/add', [ActorsController::class, 'favorite'])->name('actor.favorite');
    Route::post('favorites/actor/remove', [ActorsController::class, 'unfavorite'])->name('actor.unfavorite');

    Route::get('settings', SettingsController::class)->name('settings');
    Route::get('settings/history', [SettingsController::class, 'history'])->name('settings.history');
    Route::get('stats', StatsController::class)->name('stats');
    Route::patch('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::patch('settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');

    Route::post('billing/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('billing/success', [BillingController::class, 'success'])->name('billing.success');
    Route::get('billing/portal', [BillingController::class, 'portal'])->name('billing.portal');

    Route::middleware('throttle:history')->group(function () {
        Route::post('history/touch', [WatchHistoryController::class, 'touch'])->name('history.touch');
        Route::post('history/mark-watched', [WatchHistoryController::class, 'markWatched'])->name('history.mark');
        Route::post('history/bump', [WatchHistoryController::class, 'bump'])->name('history.bump');
    });

    Route::delete('history/clear', [WatchHistoryController::class, 'clear'])->name('history.clear');
    Route::delete('history/{history}', [WatchHistoryController::class, 'destroy'])->name('history.destroy');

    Route::middleware('throttle:comments')->group(function () {
        Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
        Route::patch('comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('comments/{comment}/like', [CommentController::class, 'like'])->name('comments.like');
    });
});
