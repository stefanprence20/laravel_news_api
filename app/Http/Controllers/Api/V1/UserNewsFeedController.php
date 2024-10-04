<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Services\UserNewsFeedService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserNewsFeedController extends Controller
{
    protected UserNewsFeedService $newsFeedService;

    public function __construct(UserNewsFeedService $newsFeedService)
    {
        $this->newsFeedService = $newsFeedService;
    }

    /**
     * Display a listing of personalized user news feed.
     */
    public function getNewsFeed(Request $request): AnonymousResourceCollection
    {
        $articles = $this->newsFeedService->getNewsFeed($request);

        return ArticleResource::collection($articles);
    }
}
