<?php

namespace App\Http\Controllers\Auth;

use App\Auth\Services\GuestDataMergeService;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class AuthController extends Controller
{
    public function __construct(
        private readonly GuestDataMergeService $guestMerge,
    ) {}

    public function register(Request $request): RedirectResponse
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'guest_data' => ['nullable', 'string'],
        ]);

        $user = User::query()->create(collect($fields)->only(['name', 'email', 'password'])->all());
        $user->assignRole(Role::User->value);
        Auth::login($user);

        event(new Registered($user));

        $this->mergeGuestData($user, $fields['guest_data'] ?? null);

        return redirect()->route('verification.notice');
    }

    public function login(Request $request): RedirectResponse
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'guest_data' => ['nullable', 'string'],
        ]);

        if (Auth::attempt(collect($fields)->only(['email', 'password'])->all(), $request->remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            $this->mergeGuestData($user, $fields['guest_data'] ?? null);

            if (! $user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');

    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    private function mergeGuestData(User $user, ?string $guestData): void
    {
        if ($guestData === null || $guestData === '') {
            return;
        }

        $payload = json_decode($guestData, true);

        if (! is_array($payload)) {
            return;
        }

        $this->guestMerge->merge($user, $payload);
    }
}

