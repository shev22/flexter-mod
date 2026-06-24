<?php

namespace App\WatchHistory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\WatchHistory\Services\Interfaces\WatchHistoryServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WatchHistoryController extends Controller
{
    public function __construct(private readonly WatchHistoryServiceInterface $history)
    {
    }

    public function touch(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['movie', 'tv'])],
            'id' => ['required', 'integer'],
            'progress' => ['nullable', 'integer', 'min:1', 'max:100'],
            'season' => ['nullable', 'integer', 'min:0'],
            'episode' => ['nullable', 'integer', 'min:0'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $progress = $this->history->touch(
            $user,
            $validated['type'],
            (int) $validated['id'],
            (int) ($validated['progress'] ?? 15),
            isset($validated['season']) ? (int) $validated['season'] : null,
            isset($validated['episode']) ? (int) $validated['episode'] : null,
        );

        if ($request->wantsJson()) {
            return response()->json(['progress' => $progress]);
        }

        return back();
    }

    public function markWatched(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['movie', 'tv'])],
            'id' => ['required', 'integer'],
            'season' => ['nullable', 'integer', 'min:0'],
            'episode' => ['nullable', 'integer', 'min:0'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $this->history->markWatched(
            $user,
            $validated['type'],
            (int) $validated['id'],
            isset($validated['season']) ? (int) $validated['season'] : null,
            isset($validated['episode']) ? (int) $validated['episode'] : null,
        );

        return back();
    }

    public function bump(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['movie', 'tv'])],
            'id' => ['required', 'integer'],
            'progress' => ['required', 'integer', 'min:1', 'max:100'],
            'season' => ['nullable', 'integer', 'min:0'],
            'episode' => ['nullable', 'integer', 'min:0'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $progress = $this->history->bumpProgress(
            $user,
            $validated['type'],
            (int) $validated['id'],
            (int) $validated['progress'],
            isset($validated['season']) ? (int) $validated['season'] : null,
            isset($validated['episode']) ? (int) $validated['episode'] : null,
        );

        if ($request->wantsJson()) {
            return response()->json(['progress' => $progress]);
        }

        return back();
    }

    public function destroy(int $history): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $this->history->remove($user, $history);

        return back();
    }

    public function clear(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $this->history->clear($user);

        return back();
    }
}
