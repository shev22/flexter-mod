<?php

namespace App\List\Http\Controllers;

use App\Http\Controllers\Controller;
use App\List\Models\FlexterList;
use App\List\Services\UserListService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserListController extends Controller
{
    public function __construct(
        private readonly UserListService $lists,
    ) {}

    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Lists/MyLists', [
            'lists' => $this->lists->mine($user),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'visibility' => ['nullable', Rule::in(['public', 'private'])],
            'icon' => ['nullable', 'string', 'max:32'],
        ]);

        $list = $this->lists->create($user, $validated);

        return redirect()
            ->route('my.lists.show', $list['slug'])
            ->with('success', 'List created.');
    }

    public function show(FlexterList $list): Response
    {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($list->user_id === $user->id, 404);

        $presented = $this->lists->show($user, $list);

        abort_if($presented === null, 404);

        return Inertia::render('Lists/MyShow', [
            'list' => $presented,
        ]);
    }

    public function addItem(Request $request, FlexterList $list): RedirectResponse
    {
        $validated = $request->validate([
            'media_type' => ['required', Rule::in(['movie', 'tv'])],
            'media_id' => ['required', 'integer'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $this->lists->addItem($user, $list, $validated['media_type'], (int) $validated['media_id']);

        return back()->with('success', 'Added to list.');
    }

    public function removeItem(Request $request, FlexterList $list): RedirectResponse
    {
        $validated = $request->validate([
            'media_type' => ['required', Rule::in(['movie', 'tv'])],
            'media_id' => ['required', 'integer'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $this->lists->removeItem($user, $list, $validated['media_type'], (int) $validated['media_id']);

        return back()->with('success', 'Removed from list.');
    }

    public function options(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'media_type' => ['required', Rule::in(['movie', 'tv'])],
            'media_id' => ['required', 'integer'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        return response()->json([
            'lists' => $this->lists->optionsForMedia(
                $user,
                $validated['media_type'],
                (int) $validated['media_id'],
            ),
        ]);
    }
}
