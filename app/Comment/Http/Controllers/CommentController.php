<?php

namespace App\Comment\Http\Controllers;

use App\Comment\Models\Comment;
use App\Comment\Services\Interfaces\CommentServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentServiceInterface $comments,
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'media_type' => ['required', Rule::in(['movie', 'tv'])],
            'media_id' => ['required', 'integer'],
            'body' => ['required', 'string', 'min:1', 'max:2000'],
            'is_spoiler' => ['sometimes', 'boolean'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            'sort' => ['sometimes', Rule::in(['newest', 'top'])],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $this->comments->store($user, $validated);

        return back()->with('success', 'Comment posted.');
    }

    public function update(Request $request, Comment $comment): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
            'is_spoiler' => ['sometimes', 'boolean'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $this->comments->update($user, $comment, $validated);

        return back()->with('success', 'Comment updated.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $this->comments->delete($user, $comment);

        return back()->with('success', 'Comment removed.');
    }

    public function like(Comment $comment): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $this->comments->toggleLike($user, $comment);

        return back();
    }
}
