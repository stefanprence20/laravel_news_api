<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    public ArticleService $service;

    public function __construct(ArticleService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $articles = $this->service->paginate($request);

        return ArticleResource::collection($articles);
    }

    /**
     * Display a listing of the resource after search.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $articles = $this->service->search($request);

        return ArticleResource::collection($articles);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article): ArticleResource
    {
        return new ArticleResource($article->load(['authors', 'source']));
    }
}
