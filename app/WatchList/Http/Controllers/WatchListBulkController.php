<?php

namespace App\WatchList\Http\Controllers;

use App\Actor\Models\Actor;
use App\Http\Controllers\Controller;
use App\Movie\Models\Movie;
use App\Tv\Models\Tv;
use App\Models\User;
use App\WatchList\Services\Interfaces\WatchListServiceInterface;
use App\Shared\Support\Watchlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WatchListBulkController extends Controller
{
    public function __construct(private readonly WatchListServiceInterface $watchListService) {}

    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.type' => ['required', Rule::in(['movie', 'tv', 'actor'])],
        ]);

        /** @var User $user */
        $user = Auth::user();

        foreach (collect($validated['items'])->groupBy('type') as $type => $items) {
            $mediaType = match ($type) {
                'tv' => Tv::class,
                'actor' => Actor::class,
                default => Movie::class,
            };

            $ids = collect($items)->pluck('id')->map(fn ($id) => (int) $id)->all();

            $user->watchlist()
                ->where('media_type', $mediaType)
                ->whereIn('media_id', $ids)
                ->delete();
        }

        Watchlist::reset();

        return back()->with('success', 'Removed selected items from your watchlist.');
    }
}
