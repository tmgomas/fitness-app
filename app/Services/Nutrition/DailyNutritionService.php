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

            // Get the user's base recommended calories
            $recommended = $this->getRecommendedCalories($user);
            $baseRecommendedCalories = $recommended['total_calories'] ?? 2000;

            // Calculate totals
            $totalCaloriesConsumed = $this->calculateTotalCaloriesFromLogs($foodLogs);
            $totalCaloriesBurned = abs($exerciseLogs->sum('calories_burned')); // Ensure positive
            $totalRealCaloriesBurned = abs($exerciseLogs->sum('real_calories_burned')); // Ensure positive

            // Update recommended calories to include burned calories
            $updatedRecommendedCalories = $baseRecommendedCalories + $totalCaloriesBurned;

            // Remaining Calories Calculation
            $remainingCalories = $updatedRecommendedCalories - $totalCaloriesConsumed;

            // Get detailed nutrition breakdown
            $nutritionBreakdown = $this->calculateNutritionBreakdown($foodLogs);
            $mealTypeSummary = $this->getMealTypeSummary($foodLogs);

            return [
                'date' => $targetDate->format('Y-m-d'),
                'user_id' => $user->id,
                'calories' => [
                    'consumed' => round($totalCaloriesConsumed, 2),
                    'burned' => round($totalRealCaloriesBurned, 2),
                    'recommended' => round($updatedRecommendedCalories, 2), // Base + Burned
                    'remaining' => round($remainingCalories, 2)
                ],
                'base_recommended' => round($baseRecommendedCalories, 2), // Base calories separately
                'nutrition_breakdown' => $nutritionBreakdown,
                'meal_types' => $mealTypeSummary,
                'food_logs_count' => $foodLogs->count(),
                'meal_logs_count' => 0,
                'exercise_logs_count' => $exerciseLogs->count()
            ];
        } catch (\Exception $e) {
            Log::error('Error in getNutritionSummary: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return [
                'date' => $date ?? Carbon::today()->format('Y-m-d'),
                'user_id' => $user->id,
                'calories' => [
                    'consumed' => 0,
                    'burned' => 0,
                    'recommended' => 2000, // Base + Burned
                    'remaining' => 2000
                ],
                'base_recommended' => 2000, // Base calories separately
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
    //   public function getRecommendedCalories(User $user): array
    //     {
    //         try {
    //             // Get the latest health data
    //             $healthData = UserHealthData::where('user_id', $user->id)
    //                 ->orderBy('recorded_at', 'desc')
    //                 ->first();

    //             Log::info('Retrieved health data for user', [
    //                 'user_id' => $user->id,
    //                 'has_health_data' => !is_null($healthData),
    //                 'health_data_id' => $healthData ? $healthData->health_id : null,
    //                 'recorded_at' => $healthData ? $healthData->recorded_at : null
    //             ]);

    //             // Get user preferences
    //             $preferences = UserPreference::where('user_id', $user->id)
    //                 ->orderBy('updated_at', 'desc')
    //                 ->first();

    //             Log::info('Retrieved preferences for user', [
    //                 'user_id' => $user->id,
    //                 'has_preferences' => !is_null($preferences),
    //                 'preference_id' => $preferences ? $preferences->pref_id : null,
    //                 'activity_level' => $preferences ? $preferences->activity_level : 'not set',
    //                 'fitness_goals' => $preferences ? $preferences->fitness_goals : 'not set'
    //             ]);

    //             if (!$healthData) {
    //                 // Return default values if no health data is available
    //                 return [
    //                     'total_calories' => 2000,
    //                     'base_tdee' => 2000,
    //                     'goal_adjustment' => 0,
    //                     'goal_type' => 'default',
    //                     'calculation_method' => 'default',
    //                     'parameters' => [
    //                         'gender' => 'not_set',
    //                         'age' => 30,
    //                         'height' => 0,
    //                         'weight' => 0,
    //                         'activity_level' => 'sedentary',
    //                         'fitness_goal' => 'maintain'
    //                     ],
    //                     'breakdown' => [
    //                         'carbohydrates' => 250, // 50% of calories (1000) / 4 kcal per gram
    //                         'protein' => 100, // 20% of calories (400) / 4 kcal per gram
    //                         'fat' => 67 // 30% of calories (600) / 9 kcal per gram
    //                     ]
    //                 ];
    //             }

    //             // Calculate BMR using the Mifflin-St Jeor Equation
    //             $age = $user->birthday ? Carbon::parse($user->birthday)->age : 30;
    //             $gender = $user->gender ?? 'male';
    //             $heightCm = $healthData->height;
    //             $weightKg = $healthData->weight;

    //             $bmr = 0;
    //             if ($gender === 'male') {
    //                 $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) + 5;
    //             } else {
    //                 $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) - 161;
    //             }

    //             // Log the BMR calculation for debugging
    //             Log::info('BMR Calculation', [
    //                 'user_id' => $user->id,
    //                 'gender' => $gender,
    //                 'age' => $age,
    //                 'height_cm' => $heightCm,
    //                 'weight_kg' => $weightKg,
    //                 'calculated_bmr' => $bmr
    //             ]);

    //             // Calculate BMI to determine if this is a high weight individual
    //             $heightM = $heightCm / 100; // Convert height to meters
    //             $bmi = $weightKg / ($heightM * $heightM);
    //             $isHighWeight = $bmi > 30 || $weightKg > 100;

    //             Log::info('BMI Calculation', [
    //                 'user_id' => $user->id,
    //                 'bmi' => $bmi,
    //                 'is_high_weight' => $isHighWeight
    //             ]);

    //             // Activity multiplier based on user preferences - UPDATED TO MATCH CALCULATOR.NET
    //             $activityMultiplier = 1.2; // Default: Sedentary

    //             if ($preferences) {
    //                 switch ($preferences->activity_level) {
    //                     case 'Sedentary':
    //                         // Little or no exercise
    //                         $activityMultiplier = 1.2;
    //                         break;
    //                     case 'Lightly Active':
    //                         // Light exercise 1-3 times/week
    //                         $activityMultiplier = 1.375;
    //                         break;
    //                     case 'Moderately Active':
    //                         // Moderate exercise 4-5 times/week
    //                         $activityMultiplier = 1.4649; // Updated to match Calculator.net
    //                         break;
    //                     case 'Active':
    //                         // Daily exercise or intense exercise 3-4 times/week
    //                         $activityMultiplier = 1.55;
    //                         break;
    //                     case 'Very Active':
    //                         // Intense exercise 6-7 times/week
    //                         $activityMultiplier = 1.725;
    //                         break;
    //                     case 'Extremely Active':
    //                         // Very intense exercise daily, or physical job
    //                         $activityMultiplier = 1.9;
    //                         break;
    //                     // Handle legacy values
    //                     case 'Very Active':
    //                         $activityMultiplier = 1.725;
    //                         break;
    //                     default:
    //                         $activityMultiplier = 1.2; // Default to sedentary
    //                         Log::info('Using default activity multiplier due to unrecognized activity level', [
    //                             'activity_level' => $preferences->activity_level
    //                         ]);
    //                 }
    //             }

    //             Log::info('Activity Level Debug', [
    //                 'activity_level' => $preferences->activity_level,
    //                 'length' => strlen($preferences->activity_level),
    //                 'binary_representation' => bin2hex($preferences->activity_level),
    //                 'trimmed_value' => trim($preferences->activity_level)
    //             ]);

    //             // Calculate TDEE (Total Daily Energy Expenditure)
    //             $tdee = $bmr * $activityMultiplier;

    //             // Store the base TDEE (this matches Calculator.net value)
    //             $baseTdee = $tdee;

    //             // Log the TDEE calculation for debugging
    //             Log::info('TDEE Calculation', [
    //                 'user_id' => $user->id,
    //                 'bmr' => $bmr,
    //                 'activity_level' => $preferences ? $preferences->activity_level : 'Not set',
    //                 'activity_multiplier' => $activityMultiplier,
    //                 'calculated_tdee' => $tdee
    //             ]);

    //             // Define goal adjustment percentages based on activity level and weight status
    //             $mildWeightLossPercent = 0.9; // Default 90%
    //             $weightLossPercent = 0.8;     // Default 80%
    //             $extremeWeightLossPercent = 0.6; // Default 60%

    //             // Set different percentages based on activity level and weight status
    //             if ($preferences) {
    //                 if ($isHighWeight) {
    //                     // Higher percentages for high weight individuals - based on Calculator.net analysis
    //                     switch ($preferences->activity_level) {
    //                         case 'Sedentary':
    //                             $mildWeightLossPercent = 0.90;
    //                             $weightLossPercent = 0.85;
    //                             $extremeWeightLossPercent = 0.75;
    //                             break;
    //                         case 'Lightly Active':
    //                             $mildWeightLossPercent = 0.92;
    //                             $weightLossPercent = 0.86;
    //                             $extremeWeightLossPercent = 0.76;
    //                             break;
    //                         case 'Moderately Active':
    //                             // Exact values from Calculator.net for high weight individuals
    //                             $mildWeightLossPercent = 0.939570;
    //                             $weightLossPercent = 0.879139;
    //                             $extremeWeightLossPercent = 0.758279;
    //                             break;
    //                         case 'Active':
    //                             $mildWeightLossPercent = 0.94;
    //                             $weightLossPercent = 0.88;
    //                             $extremeWeightLossPercent = 0.76;
    //                             break;
    //                         case 'Very Active':
    //                             $mildWeightLossPercent = 0.94;
    //                             $weightLossPercent = 0.89;
    //                             $extremeWeightLossPercent = 0.77;
    //                             break;
    //                         case 'Extremely Active':
    //                             $mildWeightLossPercent = 0.95;
    //                             $weightLossPercent = 0.90;
    //                             $extremeWeightLossPercent = 0.78;
    //                             break;
    //                         default:
    //                             $mildWeightLossPercent = 0.939570;
    //                             $weightLossPercent = 0.879139;
    //                             $extremeWeightLossPercent = 0.758279;
    //                             break;
    //                     }
    //                 } else {
    //                     // Standard percentages for normal weight individuals
    //                     switch ($preferences->activity_level) {
    //                         case 'Sedentary':
    //                             // Little or no exercise
    //                             $mildWeightLossPercent = 0.870197;
    //                             $weightLossPercent = 0.740395;
    //                             $extremeWeightLossPercent = 0.480789;
    //                             break;
    //                         case 'Lightly Active':
    //                             // Light exercise 1-3 times/week
    //                             $mildWeightLossPercent = 0.890;
    //                             $weightLossPercent = 0.780;
    //                             $extremeWeightLossPercent = 0.561;
    //                             break;
    //                         case 'Moderately Active':
    //                             // Moderate exercise 4-5 times/week
    //                             $mildWeightLossPercent = 0.889184;
    //                             $weightLossPercent = 0.778342;
    //                             $extremeWeightLossPercent = 0.556684;
    //                             break;
    //                         case 'Active':
    //                             // Daily exercise or intense exercise 3-4 times/week
    //                             $mildWeightLossPercent = 0.903;
    //                             $weightLossPercent = 0.805;
    //                             $extremeWeightLossPercent = 0.610;
    //                             break;
    //                         case 'Very Active':
    //                             // Intense exercise 6-7 times/week
    //                             $mildWeightLossPercent = 0.912;
    //                             $weightLossPercent = 0.825;
    //                             $extremeWeightLossPercent = 0.650;
    //                             break;
    //                         case 'Extremely Active':
    //                             // Very intense exercise daily, or physical job
    //                             $mildWeightLossPercent = 0.921;
    //                             $weightLossPercent = 0.841;
    //                             $extremeWeightLossPercent = 0.682;
    //                             break;
    //                         default:
    //                             // Default to Calculator.net standard percentages
    //                             $mildWeightLossPercent = 0.9;
    //                             $weightLossPercent = 0.8;
    //                             $extremeWeightLossPercent = 0.6;
    //                             Log::info('Using default weight loss percentages due to unrecognized activity level', [
    //                                 'activity_level' => $preferences->activity_level
    //                             ]);
    //                             break;
    //                     }
    //                 }
    //             }

    //             // Adjust based on fitness goals
    //             $goalAdjustment = 0;
    //             $goalType = '';

    //             if ($preferences) {
    //                 switch ($preferences->fitness_goals) {
    //                     case 'Weight Loss':
    //                         // Updated to use varying percentages based on activity level
    //                         $goalAdjustment = $tdee * $weightLossPercent - $tdee;
    //                         $goalType = 'weight_loss';
    //                         break;
    //                     case 'Extreme Weight Loss':
    //                         // Updated to use varying percentages based on activity level
    //                         $goalAdjustment = $tdee * $extremeWeightLossPercent - $tdee;
    //                         $goalType = 'extreme_weight_loss';
    //                         break;
    //                     case 'Weight Gain':
    //                         $goalAdjustment = $tdee * 0.1; // Add 10% for weight gain
    //                         $goalType = 'weight_gain';
    //                         break;
    //                     case 'Build Muscle':
    //                         $goalAdjustment = $tdee * 0.2; // Add 20% for muscle building
    //                         $goalType = 'build_muscle';
    //                         break;
    //                     case 'Mild Weight Loss':
    //                         // Updated to use varying percentages based on activity level
    //                         $goalAdjustment = $tdee * $mildWeightLossPercent - $tdee;
    //                         $goalType = 'mild_weight_loss';
    //                         break;
    //                     case 'Maintain Weight':
    //                         $goalAdjustment = 0; // No adjustment for weight maintenance
    //                         $goalType = 'maintain_weight';
    //                         break;
    //                     case 'Other':
    //                         $goalAdjustment = 0; // Default no adjustment for other goals
    //                         $goalType = 'other';
    //                         break;
    //                     default:
    //                         $goalAdjustment = 0; // Default if no specific goal is set
    //                         $goalType = 'not_set';
    //                 }
    //             }

    //             // Apply goal adjustment, ensuring we don't go below 1200 calories
    //             $adjustedTdee = max(1200, $tdee + $goalAdjustment);

    //             // Use adjusted TDEE for the traditional return value (backwards compatibility)
    //             $recommendedCalories = $adjustedTdee;

    //             // Log the goal adjustment for debugging
    //             Log::info('Goal Adjustment', [
    //                 'user_id' => $user->id,
    //                 'fitness_goal' => $preferences ? $preferences->fitness_goals : 'Not set',
    //                 'goal_type' => $goalType,
    //                 'activity_level' => $preferences ? $preferences->activity_level : 'Not set',
    //                 'is_high_weight' => $isHighWeight,
    //                 'bmi' => round($bmi, 1),
    //                 'mild_weight_loss_percent' => $mildWeightLossPercent,
    //                 'weight_loss_percent' => $weightLossPercent,
    //                 'extreme_weight_loss_percent' => $extremeWeightLossPercent,
    //                 'adjustment_amount' => $goalAdjustment,
    //                 'base_tdee' => $baseTdee,
    //                 'adjusted_tdee' => $adjustedTdee,
    //                 'final_recommendation' => $recommendedCalories
    //             ]);

    //             // Macro breakdown
    //             // Default: 50% carbs, 20% protein, 30% fat
    //             $carbs = ($recommendedCalories * 0.5) / 4; // 4 calories per gram of carbs
    //             $protein = ($recommendedCalories * 0.2) / 4; // 4 calories per gram of protein
    //             $fat = ($recommendedCalories * 0.3) / 9; // 9 calories per gram of fat

    //             return [
    //                 'total_calories' => round($recommendedCalories), // Traditional return value (with goal adjustment)
    //                 'base_tdee' => round($baseTdee), // New value for comparison with Calculator.net
    //                 'goal_adjustment' => $goalAdjustment, // How much we adjusted by
    //                 'goal_type' => $goalType, // What kind of goal was applied
    //                 'calculation_method' => 'mifflin_st_jeor',
    //                 'is_high_weight' => $isHighWeight, // Add BMI information
    //                 'bmi' => round($bmi, 1), // Add BMI value
    //                 'parameters' => [
    //                     'gender' => $gender,
    //                     'age' => $age,
    //                     'height' => $heightCm,
    //                     'weight' => $weightKg,
    //                     'activity_level' => $preferences->activity_level ?? 'sedentary',
    //                     'fitness_goal' => $preferences->fitness_goals ?? 'maintain'
    //                 ],
    //                 'weight_loss_calories' => [
    //                     'mild' => round($baseTdee * $mildWeightLossPercent),
    //                     'medium' => round($baseTdee * $weightLossPercent),
    //                     'extreme' => round($baseTdee * $extremeWeightLossPercent)
    //                 ],
    //                 'percentages' => [
    //                     'mild' => round($mildWeightLossPercent * 100),
    //                     'medium' => round($weightLossPercent * 100),
    //                     'extreme' => round($extremeWeightLossPercent * 100)
    //                 ],
    //                 'breakdown' => [
    //                     'carbohydrates' => round($carbs),
    //                     'protein' => round($protein),
    //                     'fat' => round($fat)
    //                 ]
    //             ];
    //         } catch (\Exception $e) {
    //             Log::error('Error in getRecommendedCalories: ' . $e->getMessage(), [
    //                 'user_id' => $user->id,
    //                 'trace' => $e->getTraceAsString()
    //             ]);

    //             // Return default values on error
    //             return [
    //                 'total_calories' => 2000,
    //                 'base_tdee' => 2000,
    //                 'goal_adjustment' => 0,
    //                 'goal_type' => 'error',
    //                 'calculation_method' => 'default',
    //                 'parameters' => [
    //                     'gender' => 'not_set',
    //                     'age' => 30,
    //                     'height' => 0,
    //                     'weight' => 0,
    //                     'activity_level' => 'sedentary',
    //                     'fitness_goal' => 'maintain'
    //                 ],
    //                 'breakdown' => [
    //                     'carbohydrates' => 250,
    //                     'protein' => 100,
    //                     'fat' => 67
    //                 ],
    //                 'error' => $e->getMessage()
    //             ];
    //         }
    //     }


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

            // Calculate BMR using the Mifflin-St Jeor Equation by default
            $age = $user->birthday ? Carbon::parse($user->birthday)->age : 30;
            $gender = $user->gender ?? 'male';
            $heightCm = $healthData->height;
            $weightKg = $healthData->weight;
            $calculationMethod = 'mifflin_st_jeor'; // Default method

            // Calculate BMR based on selected formula (defaulting to Mifflin-St Jeor)
            $bmr = $this->calculateBMR($gender, $age, $heightCm, $weightKg, $calculationMethod);

            // Log the BMR calculation for debugging
            Log::info('BMR Calculation', [
                'user_id' => $user->id,
                'gender' => $gender,
                'age' => $age,
                'height_cm' => $heightCm,
                'weight_kg' => $weightKg,
                'calculated_bmr' => $bmr,
                'calculation_method' => $calculationMethod
            ]);

            // Calculate BMI to determine if this is a high weight individual (keep for information)
            $heightM = $heightCm / 100; // Convert height to meters
            $bmi = $weightKg / ($heightM * $heightM);
            $isHighWeight = $bmi > 30 || $weightKg > 100;

            Log::info('BMI Calculation', [
                'user_id' => $user->id,
                'bmi' => $bmi,
                'is_high_weight' => $isHighWeight
            ]);

            // Activity multiplier based on user preferences - UPDATED TO MATCH REACT CALCULATOR
            // These are the exact multipliers from the React implementation
            $activityMultipliers = [
                'Sedentary' => 1.2,            // Little or no exercise
                'Lightly Active' => 1.375,     // Light exercise 1-3 times/week
                'Moderately Active' => 1.465,  // Moderate exercise 4-5 times/week - Updated to match React
                'Active' => 1.55,              // Daily exercise or intense exercise 3-4 times/week
                'Very Active' => 1.725,        // Intense exercise 6-7 times/week
                'Extremely Active' => 1.9      // Very intense exercise daily, or physical job
            ];

            // Default to sedentary if preference is not set or invalid
            $activityLevel = $preferences ? $preferences->activity_level : 'Sedentary';
            $activityMultiplier = $activityMultipliers[$activityLevel] ?? 1.2;

            Log::info('Activity Level', [
                'activity_level' => $activityLevel,
                'multiplier' => $activityMultiplier
            ]);

            // Calculate TDEE (Total Daily Energy Expenditure)
            $tdee = $bmr * $activityMultiplier;
            $baseTdee = round($tdee); // Store the base TDEE rounded

            // Log the TDEE calculation for debugging
            Log::info('TDEE Calculation', [
                'user_id' => $user->id,
                'bmr' => $bmr,
                'activity_level' => $activityLevel,
                'activity_multiplier' => $activityMultiplier,
                'calculated_tdee' => $tdee
            ]);

            // Calculate weight loss targets using fixed calorie deficits like in the React implementation
            $mildWeightLoss = round($baseTdee - 250);     // 250 calorie deficit
            $weightLoss = round($baseTdee - 500);         // 500 calorie deficit
            $extremeWeightLoss = round($baseTdee - 1000); // 1000 calorie deficit

            // Calculate the percentages these targets represent of maintenance calories
            $mildPercent = round(($mildWeightLoss / $baseTdee) * 100);
            $weightLossPercent = round(($weightLoss / $baseTdee) * 100);
            $extremePercent = round(($extremeWeightLoss / $baseTdee) * 100);

            // Apply goal adjustment based on the user's fitness goal
            $goalAdjustment = 0;
            $goalType = 'maintain_weight'; // Default
            $recommendedCalories = $baseTdee; // Default to maintenance

            if ($preferences) {
                switch ($preferences->fitness_goals) {
                    case 'Weight Loss':
                        $goalAdjustment = -500; // Fixed 500 calorie deficit
                        $recommendedCalories = $weightLoss;
                        $goalType = 'weight_loss';
                        break;
                    case 'Extreme Weight Loss':
                        $goalAdjustment = -1000; // Fixed 1000 calorie deficit
                        $recommendedCalories = $extremeWeightLoss;
                        $goalType = 'extreme_weight_loss';
                        break;
                    case 'Mild Weight Loss':
                        $goalAdjustment = -250; // Fixed 250 calorie deficit
                        $recommendedCalories = $mildWeightLoss;
                        $goalType = 'mild_weight_loss';
                        break;
                    case 'Weight Gain':
                        $goalAdjustment = round($baseTdee * 0.1); // Add 10% for weight gain
                        $recommendedCalories = $baseTdee + $goalAdjustment;
                        $goalType = 'weight_gain';
                        break;
                    case 'Build Muscle':
                        $goalAdjustment = round($baseTdee * 0.2); // Add 20% for muscle building
                        $recommendedCalories = $baseTdee + $goalAdjustment;
                        $goalType = 'build_muscle';
                        break;
                    case 'Maintain Weight':
                        $goalAdjustment = 0; // No adjustment
                        $recommendedCalories = $baseTdee;
                        $goalType = 'maintain_weight';
                        break;
                    case 'Other':
                    case 'Not Set':
                    default:
                        $goalAdjustment = 0; // No adjustment
                        $recommendedCalories = $baseTdee;
                        $goalType = 'not_set';
                        break;
                }
            }

            // Ensure minimum 1200 calories for safety
            $recommendedCalories = $recommendedCalories;

            // Log the goal adjustment for debugging
            Log::info('Goal Adjustment', [
                'user_id' => $user->id,
                'fitness_goal' => $preferences ? $preferences->fitness_goals : 'Not set',
                'goal_type' => $goalType,
                'adjustment_amount' => $goalAdjustment,
                'base_tdee' => $baseTdee,
                'recommended_calories' => $recommendedCalories,
                'mild_weight_loss' => $mildWeightLoss,
                'weight_loss' => $weightLoss,
                'extreme_weight_loss' => $extremeWeightLoss,
                'mild_percent' => $mildPercent,
                'weight_loss_percent' => $weightLossPercent,
                'extreme_percent' => $extremePercent
            ]);

            // Macro breakdown
            // Default: 50% carbs, 20% protein, 30% fat
            $carbs = ($recommendedCalories * 0.5) / 4; // 4 calories per gram of carbs
            $protein = ($recommendedCalories * 0.2) / 4; // 4 calories per gram of protein
            $fat = ($recommendedCalories * 0.3) / 9; // 9 calories per gram of fat

            return [
                'total_calories' => $recommendedCalories,
                'base_tdee' => $baseTdee,
                'goal_adjustment' => $goalAdjustment,
                'goal_type' => $goalType,
                'calculation_method' => $calculationMethod,
                'is_high_weight' => $isHighWeight,
                'bmi' => round($bmi, 1),
                'parameters' => [
                    'gender' => $gender,
                    'age' => $age,
                    'height' => $heightCm,
                    'weight' => $weightKg,
                    'activity_level' => $activityLevel,
                    'fitness_goal' => $preferences->fitness_goals ?? 'Maintain Weight'
                ],
                'weight_loss_calories' => [
                    'mild' => $mildWeightLoss,
                    'medium' => $weightLoss,
                    'extreme' => $extremeWeightLoss
                ],
                'percentages' => [
                    'mild' => $mildPercent,
                    'medium' => $weightLossPercent,
                    'extreme' => $extremePercent
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
                    'activity_level' => 'Sedentary',
                    'fitness_goal' => 'Maintain Weight'
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
     * Calculate BMR based on selected formula
     * 
     * @param string $gender User's gender ('male' or 'female')
     * @param int $age User's age
     * @param float $heightCm User's height in centimeters
     * @param float $weightKg User's weight in kilograms
     * @param string $formula Calculation formula to use ('mifflin_st_jeor', 'harris_benedict', or 'katch_mcardle')
     * @param float|null $bodyFat User's body fat percentage (required for Katch-McArdle formula)
     * @return float Calculated BMR
     */
    private function calculateBMR(string $gender, int $age, float $heightCm, float $weightKg, string &$formula, ?float $bodyFat = null): float
    {
        $bmr = 0;

        // Use Mifflin-St Jeor if body fat is not available and Katch-McArdle was requested
        if ($formula === 'katch_mcardle' && $bodyFat === null) {
            $formula = 'mifflin_st_jeor';
        }

        // Mifflin-St Jeor Equation
        if ($formula === 'mifflin_st_jeor') {
            if (strtolower($gender) === 'male') {
                $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) + 5;
            } else {
                $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) - 161;
            }
        }
        // Revised Harris-Benedict Equation
        else if ($formula === 'harris_benedict') {
            if (strtolower($gender) === 'male') {
                $bmr = 13.397 * $weightKg + 4.799 * $heightCm - 5.677 * $age + 88.362;
            } else {
                $bmr = 9.247 * $weightKg + 3.098 * $heightCm - 4.330 * $age + 447.593;
            }
        }
        // Katch-McArdle Formula (requires body fat percentage)
        else if ($formula === 'katch_mcardle' && $bodyFat !== null) {
            $bmr = 370 + 21.6 * (1 - $bodyFat / 100) * $weightKg;
        }
        // Default to Mifflin-St Jeor if formula is invalid
        else {
            $formula = 'mifflin_st_jeor';
            if (strtolower($gender) === 'male') {
                $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) + 5;
            } else {
                $bmr = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) - 161;
            }
        }

        return $bmr;
    }

    /**
     * Calculate BMR based on selected formula
     * 
     * @param string $gender User's gender ('male' or 'female')
     * @param int $age User's age
     * @param float $heightCm User's height in centimeters
     * @param float $weightKg User's weight in kilograms
     * @param string $formula Calculation formula to use ('mifflin_st_jeor', 'harris_benedict', or 'katch_mcardle')
     * @param float|null $bodyFat User's body fat percentage (required for Katch-McArdle formula)
     * @return float Calculated BMR
     */

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
