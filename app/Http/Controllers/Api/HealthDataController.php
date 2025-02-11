<?php
// app/Http/Controllers/Api/HealthDataController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HealthData\StoreHealthDataRequest;
use App\Http\Requests\HealthData\UpdateHealthDataRequest;
use App\Http\Resources\HealthDataResource;
use App\Services\HealthData\Interfaces\HealthDataServiceInterface;

class HealthDataController extends Controller
{
    protected $healthDataService;

    public function __construct(HealthDataServiceInterface $healthDataService)
    {
        $this->healthDataService = $healthDataService;
    }

    public function index()
    {
        try {
            $healthData = $this->healthDataService->getAllHealthData();
            return HealthDataResource::collection($healthData);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching health data', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(StoreHealthDataRequest $request)
    {
        try {
            $healthData = $this->healthDataService->storeHealthData($request->validated());
            return new HealthDataResource($healthData);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error storing health data', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $healthData = $this->healthDataService->getHealthData($id);
            return new HealthDataResource($healthData);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Health data not found', 'error' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateHealthDataRequest $request, $id)
    {
        try {
            $healthData = $this->healthDataService->updateHealthData($id, $request->validated());
            return new HealthDataResource($healthData);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating health data', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->healthDataService->deleteHealthData($id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting health data', 'error' => $e->getMessage()], 500);
        }
    }
}
