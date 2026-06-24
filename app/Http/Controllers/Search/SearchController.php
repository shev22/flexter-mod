<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchResource;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function __construct(protected MediaApiClientInterface $apiClient) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('query', ''));

        if ($query === '' || mb_strlen($query) > 100) {
            return response()->json(['results' => []]);
        }

        $results = $this->apiClient->search($query);

        return response()->json([
            'results' => SearchResource::collection($results)->resolve(),
        ]);
    }

    public function show(Request $request): Response
    {
        $query = trim((string) $request->input('query', ''));
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 24;

        $all = ! empty($query) ? $this->apiClient->search($query, true) : collect();
        $total = $all->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        $slice = $all->forPage($page, $perPage)->values();

        return inertia('Search', [
            'results' => SearchResource::collection($slice)->resolve(),
            'query' => $query,
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }
}
