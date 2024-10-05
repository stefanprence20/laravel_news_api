<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Services\UserNewsFeedService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class UserNewsFeedController extends Controller
{
    protected UserNewsFeedService $newsFeedService;

    public function __construct(UserNewsFeedService $newsFeedService)
    {
        $this->newsFeedService = $newsFeedService;
    }

    /**
     * @OA\Get(
     *     path="/v1/users/news-feed",
     *     tags={"User News Feed"},
     *     summary="Get user news-feed",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of user articles based on preferences",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Article")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function getNewsFeed(Request $request): AnonymousResourceCollection
    {
        $articles = $this->newsFeedService->getNewsFeed($request);

        return ArticleResource::collection($articles);
    }
}
