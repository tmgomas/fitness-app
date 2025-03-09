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

use function Illuminate\Log\log;

class DailyNutritionService implements DailyNutritionServiceInterface
{
    /**
     * Get a complete nutrition summary for a user on a given date
     *
     * @param User $user
     * @param string|null $date
     * @return array
     */
    // app/Services/Nutrition/DailyNutritionService.php - getNutritionSummary method updated

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

            Log::info('Food Logs Data:', $foodLogs->toArray());

            // Get exercise logs for calculating burned calories
            $exerciseLogs = UserExerciseLog::where('user_id', $user->id)
                ->whereBetween('start_time', [$startOfDay, $endOfDay])
                ->get();

            // Get the user's recommended calories 
            $recommended = $this->getRecommendedCalories($user);
            $baseRecommendedCalories = $recommended['total_calories'] ?? 2000;

            // Calculate totals
            $totalCaloriesConsumed = $this->calculateTotalCaloriesFromLogs($foodLogs);

            // ===== වෙනස තිබෙන්නේ මෙතැන =====
            // Ensure calories burned is a positive value by using abs()
            $totalCaloriesBurned = abs($exerciseLogs->sum('calories_burned'));

            // Add burned calories to daily goal
            $adjustedRecommendedCalories = $baseRecommendedCalories + $totalCaloriesBurned;

            // Net calories is consumed calories
            $netCalories = $totalCaloriesConsumed;

            // Remaining is adjusted goal minus consumed
            $remainingCalories = $adjustedRecommendedCalories - $totalCaloriesConsumed;

            // Get detailed nutrition breakdown
            $nutritionBreakdown = $this->calculateNutritionBreakdown($foodLogs);

            // Get meal types summary 
            $mealTypeSummary = $this->getMealTypeSummary($foodLogs);

            return [
                'date' => $targetDate->format('Y-m-d'),
                'user_id' => $user->id,
                'calories' => [
                    'consumed' => round($totalCaloriesConsumed, 2),
                    'burned' => round($totalCaloriesBurned, 2), // Always positive now using abs()
                    'net' => round($netCalories, 2),
                    'recommended' => round($adjustedRecommendedCalories, 2),
                    'remaining' => round($remainingCalories, 2)
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
                    'burned' => 0, // Make sure this defaults to 0 (not -0)
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

            Log::info('Retrieved health data for user', [
                'user_id' => $user->id,
                'has_health_data' => !is_null($healthData),
                'health_data_id' => $healthData ? $healthData->health_id : null,
                'recorded_at' => $healthData ? $healthData->recorded_at : null
            ]);

            // Get user preferences
            $preferences = UserPreference::where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->first();

            Log::info('Retrieved preferences for user', [
                'user_id' => $user->id,
                'has_preferences' => !is_null($preferences),
                'preference_id' => $preferences ? $preferences->pref_id : null,
                'activity_level' => $preferences ? $preferences->activity_level : 'not set',
                'fitness_goals' => $preferences ? $preferences->fitness_goals : 'not set'
            ]);

            if (!$healthData) {
                // Return default values if no health data is available
                return [
                    'total_calories' => 2000,
                    'base_tdee' => 2000,
                    'goal_adjustment' => 0,
                    'goal_type' => 'default',
                    'calculation_method' => 'default',
                    'parameters' => [
                        'gender' => 'not_set',
                        'age' => 30,
                        'height' => 0,
                        'weight' => 0,
                        'activity_level' => 'sedentary',
                        'fitness_goal' => 'maintain'
                    ],
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

            // Log the BMR calculation for debugging
            Log::info('BMR Calculation', [
                'user_id' => $user->id,
                'gender' => $gender,
                'age' => $age,
                'height_cm' => $heightCm,
                'weight_kg' => $weightKg,
                'calculated_bmr' => $bmr
            ]);

            // Activity multiplier based on user preferences
            $activityMultiplier = 1.2; // Default: Sedentary

            if ($preferences) {
                switch ($preferences->activity_level) {
                    case 'Sedentary':
                        $activityMultiplier = 1.2;
                        break;
                    case 'Lightly Active':
                        $activityMultiplier = 1.375;
                        break;
                    case 'Moderately Active':
                        $activityMultiplier = 1.55;
                        break;
                    case 'Very Active':
                        $activityMultiplier = 1.725;
                        break;
                    case 'Extra Active':
                        $activityMultiplier = 1.9;
                        break;
                }
            }

            // Calculate TDEE (Total Daily Energy Expenditure)
            $tdee = $bmr * $activityMultiplier;

            // Store the base TDEE (this matches Calculator.net value)
            $baseTdee = $tdee;

            // Log the TDEE calculation for debugging
            Log::info('TDEE Calculation', [
                'user_id' => $user->id,
                'bmr' => $bmr,
                'activity_level' => $preferences ? $preferences->activity_level : 'Not set',
                'activity_multiplier' => $activityMultiplier,
                'calculated_tdee' => $tdee
            ]);

            // Adjust based on fitness goals
            $goalAdjustment = 0;
            $goalType = '';

            if ($preferences) {
                switch ($preferences->fitness_goals) {
                    case 'Weight Loss':
                        $goalAdjustment = -500; // Caloric deficit for weight loss
                        $goalType = 'weight_loss';
                        break;
                    case 'Weight Gain':
                        $goalAdjustment = 500; // Caloric surplus for weight gain
                        $goalType = 'weight_gain';
                        break;
                    case 'Build Muscle':
                        $goalAdjustment = 500; // Caloric surplus for muscle building
                        $goalType = 'build_muscle';
                        break;
                    case 'Improve Endurance':
                        $goalAdjustment = -250; // Slight deficit for endurance improvement
                        $goalType = 'improve_endurance';
                        break;
                    case 'Maintain Weight':
                        $goalAdjustment = 0; // No adjustment for weight maintenance
                        $goalType = 'maintain_weight';
                        break;
                    case 'Other':
                        $goalAdjustment = 0; // Default no adjustment for other goals
                        $goalType = 'other';
                        break;
                    default:
                        $goalAdjustment = 0; // Default if no specific goal is set
                        $goalType = 'not_set';
                }
            }

            // Apply goal adjustment, ensuring we don't go below 1200 calories
            $adjustedTdee = max(1200, $tdee + $goalAdjustment);

            // Use adjusted TDEE for the traditional return value (backwards compatibility)
            $recommendedCalories = $adjustedTdee;

            // Log the goal adjustment for debugging
            Log::info('Goal Adjustment', [
                'user_id' => $user->id,
                'fitness_goal' => $preferences ? $preferences->fitness_goals : 'Not set',
                'goal_type' => $goalType,
                'adjustment_amount' => $goalAdjustment,
                'base_tdee' => $baseTdee,
                'adjusted_tdee' => $adjustedTdee,
                'final_recommendation' => $recommendedCalories
            ]);

            // Macro breakdown
            // Default: 50% carbs, 20% protein, 30% fat
            $carbs = ($recommendedCalories * 0.5) / 4; // 4 calories per gram of carbs
            $protein = ($recommendedCalories * 0.2) / 4; // 4 calories per gram of protein
            $fat = ($recommendedCalories * 0.3) / 9; // 9 calories per gram of fat

            return [
                'total_calories' => round($recommendedCalories), // Traditional return value (with goal adjustment)
                'base_tdee' => round($baseTdee), // New value for comparison with Calculator.net
                'goal_adjustment' => $goalAdjustment, // How much we adjusted by
                'goal_type' => $goalType, // What kind of goal was applied
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
            Log::error('Error in getRecommendedCalories: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            // Return default values on error
            return [
                'total_calories' => 2000,
                'base_tdee' => 2000,
                'goal_adjustment' => 0,
                'goal_type' => 'error',
                'calculation_method' => 'default',
                'parameters' => [
                    'gender' => 'not_set',
                    'age' => 30,
                    'height' => 0,
                    'weight' => 0,
                    'activity_level' => 'sedentary',
                    'fitness_goal' => 'maintain'
                ],
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

        foreach ($foodLogs as $log) {
            if (!$log->foodItem || !$log->foodItem->foodNutrition) {
                Log::warning('Missing Food Item or Nutrition Data', ['log_id' => $log->id]);
                continue;
            }

            $caloriesNutrition = $log->foodItem->foodNutrition()
                ->whereIn('nutrition_id', function ($query) {
                    $query->select('nutrition_id')
                        ->from('nutrition_types')
                        ->where('name', 'like', '%calorie%')
                        ->orWhere('name', 'like', '%Calories%')
                        ->orWhere('name', 'like', '%energy%');
                })
                ->first();

            if (!$caloriesNutrition) {
                Log::warning('No Calories Nutrition Found for Log', ['log_id' => $log->id]);
                continue;
            }

            // weight_per_serving field එක භාවිතා කරමු
            $servingInGrams = 0;

            // weight_per_serving තිබේ නම් එය භාවිතා කරන්න
            if ($log->foodItem->weight_per_serving) {
                // එක සේවනයක ග්‍රෑම් ප්‍රමාණය weight_per_serving වලින් ලබා ගනිමු
                $servingInGrams = $log->foodItem->weight_per_serving * $log->serving_size;

                Log::info("Using weight_per_serving for food ID {$log->foodItem->food_id}: {$log->foodItem->weight_per_serving}g per serving");
            } else {
                // weight_per_serving නැති නම් පරණ ක්‍රමයට ගණනය කරමු
                $servingUnitToGram = $this->convertServingUnitToGrams($log->serving_unit);
                $servingInGrams = $log->serving_size * $servingUnitToGram;

                Log::info("No weight_per_serving for food ID {$log->foodItem->food_id}, using conversion: {$log->serving_unit} -> {$servingUnitToGram}g");
            }

            // 100g සේවන අනුපාතය ගණනය කරන්න
            $servingRatio = $servingInGrams / 100;

            $calculatedCalories = $caloriesNutrition->amount_per_100g * $servingRatio;
            Log::info("Calculated Calories for Log ID {$log->id}: " . $calculatedCalories, [
                'serving_size' => $log->serving_size,
                'serving_unit' => $log->serving_unit,
                'serving_in_grams' => $servingInGrams,
                'amount_per_100g' => $caloriesNutrition->amount_per_100g
            ]);

            $totalCalories += $calculatedCalories;
        }

        Log::info('Total Calories:', ['total' => $totalCalories]);
        return $totalCalories;
    }
    /**
     * Convert serving unit to equivalent grams
     *
     * @param string $unit
     * @return float
     */
    private function convertServingUnitToGrams(string $unit): float
    {
        // ඒකක පරිවර්තන සංගුණක - මෙම අගයන් ආහාර වර්ගය අනුව වෙනස් විය හැකියි
        $unitConversions = [
            'g' => 1.0, // ග්‍රෑම් සඳහා සෘජු අගය
            'kg' => 1000.0, // කිලෝග්‍රෑම්
            'mg' => 0.001, // මිලිග්‍රෑම්
            'oz' => 28.35, // අවුන්ස
            'lb' => 453.59, // පවුම්
            'cup' => 240.0, // කෝප්පය - මෙය ආහාර අනුව වෙනස් විය හැකියි
            'කෝප්ප' => 240.0, // කෝප්පය - සිංහල
            'tbsp' => 15.0, // මේස හැඳි
            'tsp' => 5.0, // තේ හැඳි
            'ml' => 1.0, // මිලිලීටර් (දියර සඳහා)
            'l' => 1000.0, // ලීටර්
            'piece' => 50.0, // කෑල්ල - මෙය ආහාර අනුව වෙනස් විය හැකියි
            'slice' => 30.0, // පෙති - මෙය ආහාර අනුව වෙනස් විය හැකියි
            'serving' => 100.0, // සේවනය - මෙය ආහාර අනුව වෙනස් විය හැකියි
        ];

        // ඒකකය පරිවර්තන සිතියමේ ඇත්දැයි පරීක්ෂා කරන්න (කුඩා අකුරු භාවිතයෙන්)
        $unit = strtolower(trim($unit));

        if (isset($unitConversions[$unit])) {
            return $unitConversions[$unit];
        }

        // පෙරනිමි අගය (ඒකකය හමු නොවුණු විට)
        Log::warning("Unknown serving unit: {$unit}, defaulting to 100g");
        return 100.0;
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

            $caloriesNutrition = $log->foodItem->foodNutrition()
                ->whereIn('nutrition_id', function ($query) {
                    $query->select('nutrition_id')
                        ->from('nutrition_types')
                        ->where('name', 'like', '%calorie%')
                        ->orWhere('name', 'like', '%Calories%')
                        ->orWhere('name', 'like', '%energy%');
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
