<?php
namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchResource;
use App\Services\MediaService\Interfaces\MediaApiClientInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;



class SearchController extends Controller
{
    public function __construct(protected MediaApiClientInterface $apiClient)
    {
    }

    public function __invoke(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        $results = $this->apiClient->search($query);

        return response()->json([
            'results' => SearchResource::collection($results)
        ]);
    }

    public function show(Request $request): Response
    {
        $query = $request->input('query');

        $results = !empty($query) ? $this->apiClient->search($query, true) : [];

        return inertia('Main/Search/Show', [
            'results' => SearchResource::collection($results),
            'query' => $query,
        ]);
    }

}
