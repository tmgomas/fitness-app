<?php

namespace App\Services\Nutrition;

use App\Models\User;
use App\Models\FoodNutrition;
use App\Models\NutritionType;
use App\Models\UserFoodLog;
use App\Models\UserExerciseLog;
use App\Models\UserHealthData;
use App\Models\UserPreference;
use App\Services\Nutrition\Interfaces\DailyNutritionServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyNutritionService implements DailyNutritionServiceInterface
{
    /**
     * Get a complete nutrition summary for a user on a given date
     *
     * @param User $user
     * @param string|null $date
     * @return array
     */
    public function getNutritionSummary(User $user, ?string $date = null): array
    {
        try {
            // Convert date string to Carbon instance or use today
            $targetDate = $date ? Carbon::parse($date) : Carbon::today();
            $startOfDay = $targetDate->copy()->startOfDay();
            $endOfDay = $targetDate->copy()->endOfDay();

            // Get all food logs for the given day
            $foodLogs = UserFoodLog::where('user_id', $user->id)
                ->whereBetween('date', [$startOfDay, $endOfDay])
                ->with(['foodItem.foodNutrition.nutritionType'])
                ->get();

            // Get exercise logs for calculating burned calories
            $exerciseLogs = UserExerciseLog::where('user_id', $user->id)
                ->whereBetween('start_time', [$startOfDay, $endOfDay])
                ->get();

            // Get the user's recommended calories 
            $recommended = $this->getRecommendedCalories($user);

            // Calculate totals
            $totalCaloriesConsumed = $this->calculateTotalCaloriesFromLogs($foodLogs);
            $totalCaloriesBurned = $exerciseLogs->sum('calories_burned');
            $netCalories = $totalCaloriesConsumed - $totalCaloriesBurned;

            // Get detailed nutrition breakdown
            $nutritionBreakdown = $this->calculateNutritionBreakdown($foodLogs);

            // Get meal types summary 
            $mealTypeSummary = $this->getMealTypeSummary($foodLogs);

            return [
                'date' => $targetDate->format('Y-m-d'),
                'user_id' => $user->id,
                'calories' => [
                    'consumed' => round($totalCaloriesConsumed, 2),
                    'burned' => round($totalCaloriesBurned, 2),
                    'net' => round($netCalories, 2),
                    'recommended' => $recommended['total_calories'] ?? 2000, // Default to 2000 if no recommendation
                    'remaining' => round(($recommended['total_calories'] ?? 2000) - $netCalories, 2)
                ],
                'nutrition_breakdown' => $nutritionBreakdown,
                'meal_types' => $mealTypeSummary,
                'food_logs_count' => $foodLogs->count(),
                'meal_logs_count' => 0,
                'exercise_logs_count' => $exerciseLogs->count()
            ];
        } catch (\Exception $e) {
            Log::error('Error in getNutritionSummary: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Return a basic structure even on error
            return [
                'date' => $date ?? Carbon::today()->format('Y-m-d'),
                'user_id' => $user->id,
                'calories' => [
                    'consumed' => 0,
                    'burned' => 0,
                    'net' => 0,
                    'recommended' => 2000,
                    'remaining' => 2000
                ],
                'nutrition_breakdown' => [],
                'meal_types' => [],
                'food_logs_count' => 0,
                'meal_logs_count' => 0,
                'exercise_logs_count' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get recommended daily calories for a user based on their health data and preferences
     *
     * @param User $user
     * @return array
     */
    public function getRecommendedCalories(User $user): array
    {
        try {
            // Get the latest health data
            $healthData = UserHealthData::where('user_id', $user->id)
                ->orderBy('recorded_at', 'desc')
                ->first();

            // Get user preferences
            $preferences = UserPreference::where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->first();

            if (!$healthData) {
                // Return default values if no health data is available
                return [
                    'total_calories' => 2000,
                    'calculation_method' => 'default',
                    'breakdown' => [
                        'carbohydrates' => 250, // 50% of calories (1000) / 4 kcal per gram
                        'protein' => 100, // 20% of calories (400) / 4 kcal per gram
                        'fat' => 67 // 30% of calories (600) / 9 kcal per gram
                    ]
                ];
            }

            // Calculate BMR using the Mifflin-St Jeor Equation
            $age = $user->birthday ? Carbon::parse($user->birthday)->age : 30;
            $gender = $user->gender ?? 'male';
            $heightCm = $healthData->height;
            $weightKg = $healthData->weight;

            $bmr = 0;
            if ($gender === 'male') {
                $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) + 5;
            } else {
                $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) - 161;
            }

            // Activity multiplier based on user preferences
            $activityMultiplier = 1.2; // Default: Sedentary

            if ($preferences) {
                switch ($preferences->activity_level) {
                    case 'sedentary':
                        $activityMultiplier = 1.2;
                        break;
                    case 'lightly_active':
                        $activityMultiplier = 1.375;
                        break;
                    case 'moderately_active':
                        $activityMultiplier = 1.55;
                        break;
                    case 'very_active':
                        $activityMultiplier = 1.725;
                        break;
                    case 'extra_active':
                        $activityMultiplier = 1.9;
                        break;
                }
            }

            // Calculate TDEE (Total Daily Energy Expenditure)
            $tdee = $bmr * $activityMultiplier;

            // Adjust based on fitness goals
            $goalAdjustment = 0;
            if ($preferences) {
                switch ($preferences->fitness_goals) {
                    case 'lose_weight':
                        $goalAdjustment = -500; // Caloric deficit
                        break;
                    case 'gain_weight':
                    case 'build_muscle':
                        $goalAdjustment = 500; // Caloric surplus
                        break;
                        // For maintenance, no adjustment needed
                }
            }

            $recommendedCalories = max(1200, $tdee + $goalAdjustment); // Never go below 1200 calories

            // Macro breakdown
            // Default: 50% carbs, 20% protein, 30% fat
            $carbs = ($recommendedCalories * 0.5) / 4; // 4 calories per gram of carbs
            $protein = ($recommendedCalories * 0.2) / 4; // 4 calories per gram of protein
            $fat = ($recommendedCalories * 0.3) / 9; // 9 calories per gram of fat

            return [
                'total_calories' => round($recommendedCalories),
                'calculation_method' => 'mifflin_st_jeor',
                'parameters' => [
                    'gender' => $gender,
                    'age' => $age,
                    'height' => $heightCm,
                    'weight' => $weightKg,
                    'activity_level' => $preferences->activity_level ?? 'sedentary',
                    'fitness_goal' => $preferences->fitness_goals ?? 'maintain'
                ],
                'breakdown' => [
                    'carbohydrates' => round($carbs),
                    'protein' => round($protein),
                    'fat' => round($fat)
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRecommendedCalories: ' . $e->getMessage());

            // Return default values on error
            return [
                'total_calories' => 2000,
                'calculation_method' => 'default',
                'breakdown' => [
                    'carbohydrates' => 250,
                    'protein' => 100,
                    'fat' => 67
                ],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get total consumed calories for a user on a given date
     *
     * @param User $user
     * @param string|null $date
     * @return float
     */
    public function getConsumedCalories(User $user, ?string $date = null): float
    {
        try {
            // Convert date string to Carbon instance or use today
            $targetDate = $date ? Carbon::parse($date) : Carbon::today();
            $startOfDay = $targetDate->copy()->startOfDay();
            $endOfDay = $targetDate->copy()->endOfDay();

            // Get all food logs for the given day
            $foodLogs = UserFoodLog::where('user_id', $user->id)
                ->whereBetween('date', [$startOfDay, $endOfDay])
                ->with(['foodItem.foodNutrition.nutritionType'])
                ->get();

            return $this->calculateTotalCaloriesFromLogs($foodLogs);
        } catch (\Exception $e) {
            Log::error('Error in getConsumedCalories: ' . $e->getMessage());
            return 0; // Return 0 on error
        }
    }

    /**
     * Get total burned calories through exercise for a user on a given date
     *
     * @param User $user
     * @param string|null $date
     * @return float
     */
    public function getBurnedCalories(User $user, ?string $date = null): float
    {
        try {
            // Convert date string to Carbon instance or use today
            $targetDate = $date ? Carbon::parse($date) : Carbon::today();
            $startOfDay = $targetDate->copy()->startOfDay();
            $endOfDay = $targetDate->copy()->endOfDay();

            // Get exercise logs for the given day
            $caloriesBurned = UserExerciseLog::where('user_id', $user->id)
                ->whereBetween('start_time', [$startOfDay, $endOfDay])
                ->sum('calories_burned');

            return (float) $caloriesBurned;
        } catch (\Exception $e) {
            Log::error('Error in getBurnedCalories: ' . $e->getMessage());
            return 0; // Return 0 on error
        }
    }

    /**
     * Calculate total calories from food logs
     *
     * @param \Illuminate\Database\Eloquent\Collection $foodLogs
     * @return float
     */
    private function calculateTotalCaloriesFromLogs($foodLogs): float
    {
        $totalCalories = 0;

        // Calculate calories from food logs
        foreach ($foodLogs as $log) {
            if (!$log->foodItem || !$log->foodItem->foodNutrition) {
                continue;
            }

            $caloriesNutrition = $log->foodItem->foodNutrition
                ->where('nutrition_id', function ($query) {
                    $query->select('nutrition_id')
                        ->from('nutrition_types')
                        ->where('name', 'like', '%calorie%')
                        ->orWhere('name', 'like', '%Calories%')
                        ->orWhere('name', 'like', '%energy%')
                        ->first();
                })
                ->first();

            if ($caloriesNutrition) {
                // Calculate based on serving size
                $servingRatio = $log->serving_size / 100; // Assuming nutrition is per 100g
                $totalCalories += $caloriesNutrition->amount_per_100g * $servingRatio;
            }
        }

        return $totalCalories;
    }

    /**
     * Calculate detailed nutrition breakdown from logs
     *
     * @param \Illuminate\Database\Eloquent\Collection $foodLogs
     * @return array
     */
    private function calculateNutritionBreakdown($foodLogs): array
    {
        // Get all nutrition types
        $nutritionTypes = NutritionType::all()->keyBy('nutrition_id');
        $breakdown = [];

        // Process food logs
        foreach ($foodLogs as $log) {
            if (!$log->foodItem || !$log->foodItem->foodNutrition) {
                continue;
            }

            foreach ($log->foodItem->foodNutrition as $nutrition) {
                $nutritionId = $nutrition->nutrition_id;
                $nutritionName = $nutritionTypes[$nutritionId]->name ?? 'Unknown';
                $servingRatio = $log->serving_size / 100; // Assuming nutrition is per 100g
                $amount = $nutrition->amount_per_100g * $servingRatio;

                if (!isset($breakdown[$nutritionId])) {
                    $breakdown[$nutritionId] = [
                        'name' => $nutritionName,
                        'unit' => $nutritionTypes[$nutritionId]->unit ?? $nutrition->measurement_unit,
                        'amount' => 0
                    ];
                }

                $breakdown[$nutritionId]['amount'] += $amount;
            }
        }

        // Convert to array and round values
        $result = [];
        foreach ($breakdown as $item) {
            $item['amount'] = round($item['amount'], 2);
            $result[] = $item;
        }

        // Sort by nutrition name
        usort($result, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $result;
    }

    /**
     * Get summary of calories by meal type
     *
     * @param \Illuminate\Database\Eloquent\Collection $foodLogs
     * @return array
     */
    private function getMealTypeSummary($foodLogs): array
    {
        $mealTypes = [
            'breakfast' => 0,
            'lunch' => 0,
            'dinner' => 0,
            'snack' => 0
        ];

        // Process food logs
        foreach ($foodLogs as $log) {
            if (!$log->foodItem || !$log->foodItem->foodNutrition) {
                continue;
            }

            $mealType = strtolower($log->meal_type);
            if (!array_key_exists($mealType, $mealTypes)) {
                $mealTypes[$mealType] = 0;
            }

            $caloriesNutrition = $log->foodItem->foodNutrition
                ->where('nutrition_id', function ($query) {
                    $query->select('nutrition_id')
                        ->from('nutrition_types')
                        ->where('name', 'like', '%calorie%')
                        ->orWhere('name', 'like', '%Calories%')
                        ->orWhere('name', 'like', '%energy%')
                        ->first();
                })
                ->first();

            if ($caloriesNutrition) {
                $servingRatio = $log->serving_size / 100;
                $mealTypes[$mealType] += $caloriesNutrition->amount_per_100g * $servingRatio;
            }
        }

        // Round values
        foreach ($mealTypes as $type => $calories) {
            $mealTypes[$type] = round($calories, 2);
        }

        return $mealTypes;
    }
}
