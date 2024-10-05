<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class ArticleController extends Controller
{
    public ArticleService $service;

    public function __construct(ArticleService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/v1/articles",
     *     tags={"Article"},
     *     summary="Get all articles",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of articles",
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
    public function index(Request $request): AnonymousResourceCollection
    {
        $articles = $this->service->paginate($request);

        return ArticleResource::collection($articles);
    }

    /**
     * @OA\Get(
     *     path="/v1/articles/search",
     *     tags={"Article"},
     *     summary="Search for articles",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/keyword"),
     *     @OA\Parameter(ref="#/components/parameters/per_page"),
     *     @OA\Parameter(ref="#/components/parameters/from_date"),
     *     @OA\Parameter(ref="#/components/parameters/to_date"),
     *     @OA\Parameter(ref="#/components/parameters/source"),
     *     @OA\Parameter(ref="#/components/parameters/author"),
     *     @OA\Response(
     *          response=200,
     *          description="A list of articles",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Article")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", description="Validation error details")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     )
     *  )
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $articles = $this->service->search($request);

        return ArticleResource::collection($articles);
    }

    /**
     * @OA\Get(
     *     path="/v1/articles/{article}",
     *     tags={"Article"},
     *     summary="Get article by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Resource not found")
     *         )
     *     )
     * )
     */
    public function show(Article $article): ArticleResource
    {
        $article = $this->service->show($article);

        return new ArticleResource($article);
    }
}
