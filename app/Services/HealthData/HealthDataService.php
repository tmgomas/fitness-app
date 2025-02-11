<?php
// app/Services/HealthData/HealthDataService.php
namespace App\Services\HealthData;

use App\Services\HealthData\Interfaces\HealthDataServiceInterface;
use App\Repositories\HealthData\Interfaces\HealthDataRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HealthDataService implements HealthDataServiceInterface
{
    protected $healthDataRepository;

    public function __construct(HealthDataRepositoryInterface $healthDataRepository)
    {
        $this->healthDataRepository = $healthDataRepository;
    }

    public function getAllHealthData()
    {
        try {
            return $this->healthDataRepository->getAllForUser(Auth::id());
        } catch (\Exception $e) {
            Log::error('Error fetching health data: ' . $e->getMessage());
            throw $e;
        }
    }

    public function storeHealthData(array $data)
    {
        try {
            return $this->healthDataRepository->createForUser(Auth::id(), $data);
        } catch (\Exception $e) {
            Log::error('Error storing health data: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getHealthData($healthId)
    {
        try {
            return $this->healthDataRepository->findForUser(Auth::id(), $healthId);
        } catch (\Exception $e) {
            Log::error('Error fetching health data: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateHealthData($healthId, array $data)
    {
        try {
            return $this->healthDataRepository->updateForUser(Auth::id(), $healthId, $data);
        } catch (\Exception $e) {
            Log::error('Error updating health data: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteHealthData($healthId)
    {
        try {
            return $this->healthDataRepository->deleteForUser(Auth::id(), $healthId);
        } catch (\Exception $e) {
            Log::error('Error deleting health data: ' . $e->getMessage());
            throw $e;
        }
    }
}
