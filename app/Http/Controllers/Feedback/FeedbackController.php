<?php

namespace App\Http\Controllers\Feedback;

use App\Feedback\Services\FeedbackService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function __construct(
        private readonly FeedbackService $feedbackService,
    ) {}

    public function show(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Main/Feedback', [
            'defaults' => [
                'name' => $user?->name ?? '',
                'email' => $user?->email ?? '',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $this->feedbackService->store($validated, $request->user());

        return back()->with('success', 'Thanks for your feedback! We\'ll get back to you soon.');
    }
}
