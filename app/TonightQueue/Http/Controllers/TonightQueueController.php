<?php

namespace App\TonightQueue\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\TonightQueue\Services\Interfaces\TonightQueueServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TonightQueueController extends Controller
{
    public function __construct(
        private readonly TonightQueueServiceInterface $queue,
    ) {}

    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return response()->json($this->queue->forUser($user));
    }

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['movie', 'tv'])],
            'id' => ['required', 'integer'],
            'title' => ['nullable', 'string', 'max:255'],
            'poster' => ['nullable', 'string', 'max:500'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $result = $this->queue->toggle($user, $validated);

        return response()->json($result);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['movie', 'tv'])],
            'id' => ['required', 'integer'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $items = $this->queue->remove($user, $validated['type'], (int) $validated['id']);

        return response()->json(['items' => $items]);
    }

    public function clear(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return response()->json(['items' => $this->queue->clear($user)]);
    }
}
