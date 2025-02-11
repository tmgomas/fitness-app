<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPreference\StoreUserPreferenceRequest;
use App\Http\Requests\UserPreference\UpdateUserPreferenceRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Services\UserPreference\Interfaces\UserPreferenceServiceInterface;
use Illuminate\Support\Facades\Log;

class UserPreferenceController extends Controller
{
    protected $preferenceService;

    public function __construct(UserPreferenceServiceInterface $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    // app/Http/Controllers/Api/UserPreferenceController.php

    public function index()
    {
        try {
            $preferences = $this->preferenceService->getUserPreferences();
            Log::info('Preferences retrieved successfully', ['count' => count($preferences)]);
            return UserPreferenceResource::collection($preferences);
        } catch (\Exception $e) {
            Log::error('Preference index error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error fetching preferences',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreUserPreferenceRequest $request)
    {
        try {
            $preference = $this->preferenceService->createPreference($request->validated());
            return new UserPreferenceResource($preference);
        } catch (\Exception $e) {
            Log::error('Error creating preference: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error creating preference',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $preference = $this->preferenceService->getPreference($id);
            return new UserPreferenceResource($preference);
        } catch (\Exception $e) {
            Log::error('Error fetching preference: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching preference',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateUserPreferenceRequest $request, $id)
    {
        try {
            $preference = $this->preferenceService->updatePreference($id, $request->validated());
            return new UserPreferenceResource($preference);
        } catch (\Exception $e) {
            Log::error('Error updating preference: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating preference',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->preferenceService->deletePreference($id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting preference: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error deleting preference',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
