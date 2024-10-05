<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="News Aggregator API",
 *      description="A RESTful API for a news aggregator service that pulls articles from various sources and provides endpoints for a frontend application to consume.",
 *      @OA\Contact(
 *          email="stefan.prence@gmail.com"
 *      ),
 * )
 *
 * @OA\Server(
 *      url="http://localhost/api",
 *      description="Local API server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string", format="password"),
 * )
 *
 * @OA\Schema(
 *      schema="Article",
 *      type="object",
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items(
 *              type="object",
 *              @OA\Property(property="id", type="integer"),
 *              @OA\Property(property="title", type="string"),
 *              @OA\Property(property="content", type="string"),
 *              @OA\Property(
 *                  property="authors",
 *                  type="array",
 *                  @OA\Items(ref="#/components/schemas/Author")
 *              ),
 *              @OA\Property(property="url", type="string", format="url"),
 *              @OA\Property(property="published_at", type="string", format="date-time"),
 *              @OA\Property(property="source", ref="#/components/schemas/Source"),
 *          )
 *      )
 *  )
 *
 * @OA\Schema(
 *      schema="Author",
 *      type="object",
 *      required={"id", "name"},
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="name", type="string")
 *  )
 *
 * @OA\Schema(
 *      schema="Source",
 *      type="object",
 *      required={"id", "name"},
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="name", type="string")
 *  )
 *
 * @OA\Schema(
 *      schema="Preference",
 *      type="object",
 *      required={"id", "user_id", "preferable_id", "preferable_type"},
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items(
 *              type="object",
 *              @OA\Property(property="id", type="integer", example=4),
 *              @OA\Property(property="user_id", type="integer", example=14),
 *              @OA\Property(property="preferable_id", type="integer", example=1),
 *              @OA\Property(property="preferable_type", type="string", example="Source")
 *          )
 *      )
 *  )
 *
 * @OA\Schema(
 *     schema="LoginResponse",
 *     type="object",
 *     @OA\Property(property="access_token", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="errors", type="object")
 * )
 *
 * @OA\Parameter(
 *     parameter="keyword",
 *     name="keyword",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string")
 * ),
 * @OA\Parameter(
 *     parameter="per_page",
 *     name="per_page",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="integer", example=10)
 * ),
 * @OA\Parameter(
 *     parameter="from_date",
 *     name="from_date",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string", format="date", example="2024-10-01")
 * ),
 * @OA\Parameter(
 *     parameter="to_date",
 *     name="to_date",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string", format="date", example="2024-10-31")
 * ),
 * @OA\Parameter(
 *     parameter="source",
 *     name="source",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string", example="The Guardian")
 * ),
 * @OA\Parameter(
 *     parameter="author",
 *     name="author",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string")
 * )
 * @OA\Parameter(
 *      name="article",
 *      in="path",
 *      required=true,
 *      @OA\Schema(type="integer", example=1)
 * ),
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
