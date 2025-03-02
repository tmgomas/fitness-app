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

    /**
     * Calculate daily nutrition totals from food logs
     * 
     * @param \Illuminate\Database\Eloquent\Collection $foodLogs
     * @return array
     */
    protected function calculateDailyNutrition($foodLogs)
    {
        $dailyTotals = [];

        foreach ($foodLogs as $log) {
            $date = $log->date->format('Y-m-d');

            if (!isset($dailyTotals[$date])) {
                $dailyTotals[$date] = [
                    'calories' => 0.0,
                    'protein' => 0.0,
                    'carbs' => 0.0,
                    'fat' => 0.0,
                    'meal_count' => 0
                ];
            }

            if ($log->foodItem && $log->foodItem->nutrition) {
                // 🚀 **Convert Serving Unit to Grams (Ensure Correct Conversion)**
                $servingUnitToGram = $this->convertServingUnitToGrams($log->serving_unit, $log->foodItem->name);
                $servingInGrams = $log->serving_size * $servingUnitToGram;

                Log::info("Food log ID {$log->id} serving details", [
                    'serving_size' => $log->serving_size,
                    'serving_unit' => $log->serving_unit,
                    'serving_in_grams' => $servingInGrams,
                ]);

                foreach ($log->foodItem->nutrition as $nutrition) {
                    if (!$nutrition->nutrition_type || $nutrition->amount_per_100g === null) {
                        continue;
                    }

                    // 🏷️ **Get Nutrition Name in Lowercase**
                    $nutritionName = strtolower(trim($nutrition->nutrition_type->name));

                    // 🍎 **Fix Calculation: Use Proper 100g Conversion**
                    $amount = round(($nutrition->amount_per_100g * $servingInGrams) / 100, 2);

                    // 🔍 **Identify Nutrition Type & Assign Values**
                    if (strpos($nutritionName, 'calorie') !== false || strpos($nutritionName, 'energy') !== false) {
                        $dailyTotals[$date]['calories'] += $amount;
                    } elseif (strpos($nutritionName, 'protein') !== false) {
                        $dailyTotals[$date]['protein'] += $amount;
                    } elseif (strpos($nutritionName, 'carbohydrate') !== false || strpos($nutritionName, 'carb') !== false) {
                        $dailyTotals[$date]['carbs'] += $amount;
                    } elseif (strpos($nutritionName, 'fat') !== false || strpos($nutritionName, 'total fat') !== false) {
                        $dailyTotals[$date]['fat'] += $amount;
                    }
                }
            }

            $dailyTotals[$date]['meal_count']++;
        }

        // 🔥 **Round Final Nutritional Values for Precision**
        foreach ($dailyTotals as $date => $nutritionData) {
            $dailyTotals[$date]['calories'] = round($nutritionData['calories'], 2);
            $dailyTotals[$date]['protein'] = round($nutritionData['protein'], 2);
            $dailyTotals[$date]['carbs'] = round($nutritionData['carbs'], 2);
            $dailyTotals[$date]['fat'] = round($nutritionData['fat'], 2);
        }

        return $dailyTotals;
    }

    /**
     * Convert serving unit to equivalent grams
     *
     * @param string $unit
     * @return float
     */
    private function convertServingUnitToGrams(string $unit, $foodItemName = null): float
    {
        $unit = strtolower(trim($unit));

        $unitConversions = [
            'g' => 1.0,
            'kg' => 1000.0,
            'mg' => 0.001,
            'oz' => 28.35,
            'lb' => 453.59,
            'ml' => 1.0,
            'l' => 1000.0,
            'cup' => 100.0, // Default 100g for rice-based foods
            'tbsp' => 15.0,
            'tsp' => 5.0,
        ];

        // Specific food items that need different conversions
        $foodSpecificConversions = [
            'Cooked White Rice' => 158.0, // 1 Cup of Rice ≈ 158g
            'Scrambled Egg' => 240.0, // 1 Cup of Scrambled Egg ≈ 240g
        ];

        if (isset($foodSpecificConversions[$foodItemName])) {
            return $foodSpecificConversions[$foodItemName];
        }

        return $unitConversions[$unit] ?? 100.0;
    }
}
