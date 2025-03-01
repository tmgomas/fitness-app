<?php

namespace App\Services\Nutrition;

use App\Models\User;
use App\Models\UserFoodLog;
use App\Models\UserExerciseLog;
use App\Models\UserHealthData;
use App\Models\UserPreference;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyNutritionService
{
    /**
     * Calculate recommended daily calorie intake based on user health data and preferences
     *
     * @param User $user
     * @return array
     */
    public function getRecommendedCalories(User $user): array
    {
        // Get the latest health data for user
        $healthData = UserHealthData::where('user_id', $user->id)
            ->latest('recorded_at')
            ->first();

        // Get user preferences
        $preferences = UserPreference::where('user_id', $user->id)
            ->latest('updated_at')
            ->first();

        if (!$healthData) {
            return [
                'calories' => 2000, // Default if no health data
                'protein' => 50,
                'carbs' => 250,
                'fat' => 70
            ];
        }

        // Get gender and age
        $gender = $user->gender ?? 'male';
        $age = $user->birthday ? Carbon::parse($user->birthday)->age : 30;

        // Calculate BMR (Basal Metabolic Rate) using Harris-Benedict Equation
        if ($gender === 'male') {
            $bmr = 88.362 + (13.397 * $healthData->weight) + (4.799 * $healthData->height) - (5.677 * $age);
        } else {
            $bmr = 447.593 + (9.247 * $healthData->weight) + (3.098 * $healthData->height) - (4.330 * $age);
        }

        // Apply activity multiplier
        $activityLevel = $preferences->activity_level ?? 'moderate';
        $activityMultipliers = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9
        ];

        $activityMultiplier = $activityMultipliers[$activityLevel] ?? 1.55;
        $maintenanceCalories = $bmr * $activityMultiplier;

        // Adjust based on fitness goals
        $fitnessGoal = $preferences->fitness_goals ?? 'maintain';
        $goalCalories = $maintenanceCalories;

        switch ($fitnessGoal) {
            case 'lose':
                $goalCalories = $maintenanceCalories - 500; // Deficit for weight loss
                break;
            case 'gain':
                $goalCalories = $maintenanceCalories + 500; // Surplus for weight gain
                break;
            default:
                // Maintenance - keep the same
                break;
        }

        // Calculate macros (approximate distribution)
        // Protein: 30%, Carbs: 40%, Fat: 30%
        $protein = ($goalCalories * 0.3) / 4; // 4 calories per gram of protein
        $carbs = ($goalCalories * 0.4) / 4;   // 4 calories per gram of carbs
        $fat = ($goalCalories * 0.3) / 9;     // 9 calories per gram of fat

        return [
            'calories' => round($goalCalories),
            'protein' => round($protein),
            'carbs' => round($carbs),
            'fat' => round($fat)
        ];
    }

    /**
     * Get total calories consumed for a specific date
     *
     * @param User $user
     * @param string|null $date
     * @return array
     */
    public function getConsumedCalories(User $user, ?string $date = null): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $startDate = $date->copy()->startOfDay();
        $endDate = $date->copy()->endOfDay();

        // Get food logs for the specified date
        $foodLogs = UserFoodLog::with(['foodItem.foodNutrition.nutritionType'])
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFat = 0;

        foreach ($foodLogs as $log) {
            if (!$log->foodItem || !$log->foodItem->foodNutrition) {
                continue;
            }

            $servingMultiplier = $log->serving_size / 100; // Convert to percentage of 100g

            foreach ($log->foodItem->foodNutrition as $nutrition) {
                if (!$nutrition->nutritionType) {
                    continue;
                }

                $amount = $nutrition->amount_per_100g * $servingMultiplier;
                $type = strtolower($nutrition->nutritionType->name);

                if (stripos($type, 'calorie') !== false) {
                    $totalCalories += $amount;
                } elseif (stripos($type, 'protein') !== false) {
                    $totalProtein += $amount;
                } elseif (stripos($type, 'carbohydrate') !== false) {
                    $totalCarbs += $amount;
                } elseif (stripos($type, 'fat') !== false) {
                    $totalFat += $amount;
                }
            }
        }

        // Get meal logs as well
        $mealLogs = DB::table('user_meal_logs')
            ->join('meals', 'user_meal_logs.meal_id', '=', 'meals.meal_id')
            ->join('meal_nutrition', 'meals.meal_id', '=', 'meal_nutrition.meal_id')
            ->join('nutrition_types', 'meal_nutrition.nutrition_id', '=', 'nutrition_types.nutrition_id')
            ->where('user_meal_logs.user_id', $user->id)
            ->whereBetween('user_meal_logs.date', [$startDate, $endDate])
            ->select(
                'user_meal_logs.serving_size',
                'nutrition_types.name as nutrition_name',
                'meal_nutrition.amount_per_100g'
            )
            ->get();

        foreach ($mealLogs as $log) {
            $servingMultiplier = $log->serving_size / 100;
            $amount = $log->amount_per_100g * $servingMultiplier;
            $type = strtolower($log->nutrition_name);

            if (stripos($type, 'calorie') !== false) {
                $totalCalories += $amount;
            } elseif (stripos($type, 'protein') !== false) {
                $totalProtein += $amount;
            } elseif (stripos($type, 'carbohydrate') !== false) {
                $totalCarbs += $amount;
            } elseif (stripos($type, 'fat') !== false) {
                $totalFat += $amount;
            }
        }

        return [
            'calories' => round($totalCalories),
            'protein' => round($totalProtein),
            'carbs' => round($totalCarbs),
            'fat' => round($totalFat)
        ];
    }

    /**
     * Get total calories burned for a specific date
     *
     * @param User $user
     * @param string|null $date
     * @return float
     */
    public function getBurnedCalories(User $user, ?string $date = null): float
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $startDate = $date->copy()->startOfDay();
        $endDate = $date->copy()->endOfDay();

        // Get exercise logs for the specified date
        $exerciseLogs = UserExerciseLog::where('user_id', $user->id)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->get();

        $totalBurned = 0;

        foreach ($exerciseLogs as $log) {
            $totalBurned += $log->calories_burned;
        }

        return round($totalBurned);
    }

    /**
     * Get complete nutrition summary for a specific date
     *
     * @param User $user
     * @param string|null $date
     * @return array
     */
    public function getNutritionSummary(User $user, ?string $date = null): array
    {
        $recommended = $this->getRecommendedCalories($user);
        $consumed = $this->getConsumedCalories($user, $date);
        $burned = $this->getBurnedCalories($user, $date);

        // Calculate remaining calories
        $remaining = $recommended['calories'] - $consumed['calories'] + $burned;

        return [
            'date' => $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
            'recommended' => $recommended,
            'consumed' => $consumed,
            'burned' => $burned,
            'remaining' => $remaining
        ];
    }
}
