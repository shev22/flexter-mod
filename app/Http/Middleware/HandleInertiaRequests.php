<?php

namespace App\Http\Middleware;

use App\Genre\Models\Genre;
use App\Shared\Data\SettingsData;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => fn () => $request->user()
                    ? array_merge(
                        $request->user()->loadMissing('roles')->only('id', 'name', 'email'),
                        ['is_admin' => $request->user()->isAdmin()],
                    )
                    : null,
            ],
            'settings' => fn () => ($request->user() && $request->user()->settings
                ? SettingsData::fromModel($request->user()->settings)
                : SettingsData::defaults())->toSharedArray(),
            'genres' => fn () => cache()->remember(
                'genres.shared',
                now()->addHours(6),
                fn () => Genre::orderBy('genre')->get()->map(fn ($g) => [
                    'id' => (int) $g->id,
                    'name' => $g->genre,
                ])->all()
            ),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'site' => function () {
                $settings = app(SiteSettingsServiceInterface::class)->get();

                return [
                    'name' => $settings->siteName,
                    'tagline' => $settings->siteTagline,
                    'maintenance' => $settings->maintenanceMode,
                    'features' => [
                        'recommendations' => $settings->enableRecommendations,
                        'actor_feed' => $settings->enableActorFeed,
                        'public_lists' => $settings->enablePublicLists,
                        'site_wide_autoplay' => $settings->siteWideAutoplay,
                    ],
                ];
            },
        ]);
    }
}
