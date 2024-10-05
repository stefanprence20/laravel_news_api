<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PreferenceResource;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class UserPreferenceController extends Controller
{
    public UserPreferenceService $userPreferenceService;

    public function __construct(UserPreferenceService $userPreferenceService)
    {
        $this->userPreferenceService = $userPreferenceService;
    }

    /**
     * @OA\Post(
     *     path="/v1/users/preferences",
     *     summary="Add user preference",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="preference_type", type="string", example="source"),
     *             @OA\Property(property="preference_ids", type="array", @OA\Items(type="integer"), example="[1,2,3]")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of recently added user preferences",
     *         @OA\JsonContent(ref="#/components/schemas/Preference")
     *     ),
     *     @OA\Response(
     *           response=422,
     *           description="Validation error",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="The given data was invalid."),
     *               @OA\Property(property="errors", type="object", description="Validation error details")
     *           )
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
    public function addPreference(Request $request): AnonymousResourceCollection
    {
        $preference = $this->userPreferenceService->addPreference($request);

        return PreferenceResource::collection($preference);
    }

    /**
     * @OA\Get(
     *     path="/v1/users/preferences",
     *     summary="Get user preferences",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(ref="#/components/parameters/per_page"),
     *     @OA\Response(
     *         response=200,
     *         description="A list of user preferences",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Preference"))
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
    public function getPreferences(Request $request): AnonymousResourceCollection
    {
        $preferences = $this->userPreferenceService->getPreferences($request);

        return PreferenceResource::collection($preferences);
    }
}
