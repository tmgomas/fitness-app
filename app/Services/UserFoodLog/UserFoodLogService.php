<?php

namespace App\Services\UserFoodLog;

use App\Services\UserFoodLog\Interfaces\UserFoodLogServiceInterface;
use App\Repositories\UserFoodLog\Interfaces\UserFoodLogRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserFoodLogService implements UserFoodLogServiceInterface
{
    protected $userFoodLogRepository;

    public function __construct(UserFoodLogRepositoryInterface $userFoodLogRepository)
    {
        $this->userFoodLogRepository = $userFoodLogRepository;
    }

    public function getAllFoodLogs(array $filters = [])
    {
        try {
            return $this->userFoodLogRepository->getFoodLogsWithFilters(Auth::id(), $filters);
        } catch (\Exception $e) {
            Log::error('Error fetching food logs: ' . $e->getMessage());
            throw $e;
        }
    }

    public function storeFoodLog(array $data)
    {
        try {
            return $this->userFoodLogRepository->createForUser(Auth::id(), $data);
        } catch (\Exception $e) {
            Log::error('Error storing food log: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getFoodLog($foodLogId)
    {
        try {
            return $this->userFoodLogRepository->findForUser(Auth::id(), $foodLogId);
        } catch (\Exception $e) {
            Log::error('Error fetching food log: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateFoodLog($foodLogId, array $data)
    {
        try {
            return $this->userFoodLogRepository->updateForUser(Auth::id(), $foodLogId, $data);
        } catch (\Exception $e) {
            Log::error('Error updating food log: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteFoodLog($foodLogId)
    {
        try {
            return $this->userFoodLogRepository->deleteForUser(Auth::id(), $foodLogId);
        } catch (\Exception $e) {
            Log::error('Error deleting food log: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getDailyNutritionReport(array $filters)
    {
        try {
            $foodLogs = $this->getAllFoodLogs($filters);
            return $this->calculateDailyNutrition($foodLogs);
        } catch (\Exception $e) {
            Log::error('Error generating daily nutrition report: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function calculateDailyNutrition($foodLogs)
    {
        $dailyTotals = [];

        foreach ($foodLogs as $log) {
            $date = $log->date->format('Y-m-d');

            if (!isset($dailyTotals[$date])) {
                $dailyTotals[$date] = [
                    'calories' => 0,
                    'protein' => 0,
                    'carbs' => 0,
                    'fat' => 0,
                    'meal_count' => 0
                ];
            }

            if ($log->foodItem && $log->foodItem->foodNutrition) {
                foreach ($log->foodItem->foodNutrition as $nutrition) {
                    $amount = ($nutrition->amount_per_100g * $log->serving_size) / 100;

                    switch ($nutrition->nutritionType->name) {
                        case 'Calories':
                            $dailyTotals[$date]['calories'] += $amount;
                            break;
                        case 'Protein':
                            $dailyTotals[$date]['protein'] += $amount;
                            break;
                        case 'Carbohydrates':
                            $dailyTotals[$date]['carbs'] += $amount;
                            break;
                        case 'Fat':
                            $dailyTotals[$date]['fat'] += $amount;
                            break;
                    }
                }
            }

            $dailyTotals[$date]['meal_count']++;
        }

        return $dailyTotals;
    }
}
