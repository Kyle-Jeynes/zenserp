<?php

namespace Zenserp\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Zenserp\Transfer\ZenserpPagination;
use Zenserp\Transfer\ZenserpRelatedSearch;
use Zenserp\Transfer\ZenserpResult;

class ZenserpController extends Controller
{
    public function search(): JsonResponse
    {
        $perPage = min((int) request('num', 10), 25);

        $response = Http::withHeaders(['apikey' => config('zenserp.api_key')])->get('https://app.zenserp.com/api/v2/search', [
            'q' => request('q'),
            'num' => $perPage,
            'start' => max(0, (int) request('start', 0)),
        ]);

        $data = collect($response->json('organic'))->map(fn ($item) => (new ZenserpResult($item))->parse())->all();
        $relatedSearches = collect($response->json('related_searches'))->map(fn ($item) => (new ZenserpRelatedSearch($item))->parse())->all();
        $pagination = new ZenserpPagination($response->json('pagination', []), $perPage);

        return response()->json([
            'data' => $data,
            'related_searches' => $relatedSearches,
            'pagination' => $pagination->parse(),
        ]);
    }
}
