<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PreferenceResource;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserPreferenceController extends Controller
{
    public UserPreferenceService $userPreferenceService;

    public function __construct(UserPreferenceService $userPreferenceService)
    {
        $this->userPreferenceService = $userPreferenceService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addPreference(Request $request): AnonymousResourceCollection
    {
        $preference = $this->userPreferenceService->addPreference($request);

        return PreferenceResource::collection($preference);
    }

    /**
     * Display the specified resource.
     */
    public function getPreferences(Request $request): AnonymousResourceCollection
    {
        $preferences = $this->userPreferenceService->getPreferences($request);

        return PreferenceResource::collection($preferences);
    }
}
