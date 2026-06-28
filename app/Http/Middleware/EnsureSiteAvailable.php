<?php

namespace App\Http\Middleware;

use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteAvailable
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SiteSettingsServiceInterface::class)->get();

        if (! $settings->maintenanceMode) {
            return $next($request);
        }

        if ($this->shouldBypassMaintenance($request)) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Flexter is temporarily down for maintenance.',
            ], 503);
        }

        return Inertia::render('Main/Maintenance', [
            'siteName' => $settings->siteName,
        ])->toResponse($request)->setStatusCode(503);
    }

    private function shouldBypassMaintenance(Request $request): bool
    {
        if (
            $request->is('admin', 'admin/*', 'livewire/*', 'filament/*')
            || $request->routeIs('filament.*', 'livewire.*')
            || $request->is('stripe/webhook', 'paypal/webhook', 'up')
        ) {
            return true;
        }

        return $request->routeIs('login', 'logout', 'register', 'password.*', 'verification.*');
    }
}
