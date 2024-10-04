<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserPreferenceService
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addPreference(Request $request): JsonResponse
    {
        $data = $request->validate([
            'preference_type' => [
                'required',
                'string',
                Rule::in(['source', 'author'])
            ],
            'preference_ids' => 'required|array',
            'preference_ids.*' => 'integer',
        ]);

        $user = $request->user();
        $preferenceType = $data['preference_type'];
        $preferenceIds = $data['preference_ids'];

        switch ($preferenceType) {
            case 'source':
                $user->sources()->sync($preferenceIds);
                break;
            case 'author':
                $user->authors()->sync($preferenceIds);
                break;
            default:
                return response()->json(['error' => 'Invalid preference type'], Response::HTTP_BAD_REQUEST);
        }

        return $user->preferences();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        return $user->preferences();
    }
}
