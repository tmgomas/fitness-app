<?php

namespace App\Services\UserMealLog;

use App\Services\UserMealLog\Interfaces\UserMealLogServiceInterface;
use App\Repositories\UserMealLog\Interfaces\UserMealLogRepositoryInterface;
use App\Services\UserMealLog\Traits\MealLogCalculationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserMealLogService implements UserMealLogServiceInterface
{

    protected $userMealLogRepository;

    public function __construct(UserMealLogRepositoryInterface $userMealLogRepository)
    {
        $this->userMealLogRepository = $userMealLogRepository;
    }

    public function getAllMealLogs(array $filters = [])
    {
        try {
            return $this->userMealLogRepository->getMealLogsWithFilters(Auth::id(), $filters);
        } catch (\Exception $e) {
            Log::error('Error fetching meal logs: ' . $e->getMessage());
            throw $e;
        }
    }

    public function storeMealLog(array $data)
    {
        try {
            return $this->userMealLogRepository->createForUser(Auth::id(), $data);
        } catch (\Exception $e) {
            Log::error('Error storing meal log: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getMealLog($mealLogId)
    {
        try {
            return $this->userMealLogRepository->findForUser(Auth::id(), $mealLogId);
        } catch (\Exception $e) {
            Log::error('Error fetching meal log: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateMealLog($mealLogId, array $data)
    {
        try {
            return $this->userMealLogRepository->updateForUser(Auth::id(), $mealLogId, $data);
        } catch (\Exception $e) {
            Log::error('Error updating meal log: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteMealLog($mealLogId)
    {
        try {
            return $this->userMealLogRepository->deleteForUser(Auth::id(), $mealLogId);
        } catch (\Exception $e) {
            Log::error('Error deleting meal log: ' . $e->getMessage());
            throw $e;
        }
    }
}
