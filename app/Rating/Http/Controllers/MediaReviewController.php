<?php

namespace App\Rating\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rating\Models\MediaReview;
use App\Rating\Services\Interfaces\MediaReviewServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MediaReviewController extends Controller
{
    public function __construct(
        private readonly MediaReviewServiceInterface $reviews,
    ) {}

    public function diary(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Diary/Index', [
            'entries' => $this->reviews->diary($user),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'media_type' => ['required', Rule::in(['movie', 'tv'])],
            'media_id' => ['required', 'integer'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:10'],
            'body' => ['nullable', 'string', 'max:2000'],
            'watched_on' => ['nullable', 'date'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $this->reviews->upsert($user, $validated);

        return back()->with('success', 'Review saved.');
    }

    public function destroy(MediaReview $review): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $this->reviews->destroy($user, $review);

        return back()->with('success', 'Review removed.');
    }
}
