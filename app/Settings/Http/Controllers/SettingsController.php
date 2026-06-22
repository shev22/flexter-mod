<?php

namespace App\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Movie\Models\Movie;
use App\Settings\Services\Interfaces\SettingsServiceInterface;
use App\Shared\Data\SettingsData;
use App\Shared\Support\Appearance;
use App\Shared\Support\Present;
use App\WatchHistory\Services\Interfaces\WatchHistoryServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingsServiceInterface $settingsService,
        private readonly WatchHistoryServiceInterface $watchHistoryService,
    ) {}

    public function __invoke(): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $settings = $this->settingsService->forUser($user);

        return Inertia::render('Settings', [
            'settings' => SettingsData::fromModel($settings)->toFullArray(),
            'stats' => $this->watchlistStats($user),
            'historyStats' => $this->watchHistoryService->stats($user),
            'memberSince' => $user->created_at?->format('M, Y'),
        ]);
    }

    public function history(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return response()->json([
            'history' => $this->watchHistoryService->recent($user)->values()->all(),
            'historyStats' => $this->watchHistoryService->stats($user),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'theme' => ['required', Rule::in(Appearance::THEMES)],
            'accent' => ['required', Rule::in(Appearance::ACCENTS)],
            'autoplay_trailers' => ['required', 'boolean'],
            'reduce_motion' => ['required', 'boolean'],
            'subtitles' => ['required', 'boolean'],
            'maturity' => ['required', Rule::in(Appearance::MATURITY)],
            'density' => ['required', Rule::in(Appearance::DENSITIES)],
            'high_contrast' => ['required', 'boolean'],
            'language' => ['required', Rule::in(Appearance::LANGUAGES)],
            'email_notifications' => ['required', 'boolean'],
            'spoiler_free' => ['required', 'boolean'],
            'favorite_genre_ids' => ['nullable', 'array', 'max:5'],
            'favorite_genre_ids.*' => ['integer', 'exists:genres,id'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $this->settingsService->update($user, $validated);

        return back()->with('success', 'Preferences saved.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $this->settingsService->updateProfile($user, $validated);

        return back()->with('success', 'Profile updated.');
    }

    /** @return array<string, int> */
    private function watchlistStats(User $user): array
    {
        $counts = $user->watchlist()
            ->select('media_type', DB::raw('COUNT(*) as total'))
            ->groupBy('media_type')
            ->pluck('total', 'media_type');

        return [
            'watchlist' => (int) $counts->sum(),
            'movies' => (int) ($counts['App\\Movie\\Models\\Movie'] ?? 0),
            'shows' => (int) ($counts['App\\Tv\\Models\\Tv'] ?? 0),
            'actors' => (int) ($counts['App\\Actor\\Models\\Actor'] ?? 0),
        ];
    }
}
