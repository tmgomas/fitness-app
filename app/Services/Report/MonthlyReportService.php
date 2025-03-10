<?php

namespace App\Services\Report;

use App\Models\User;
use App\Models\UserExerciseLog;
use App\Models\UserFoodLog;
use App\Models\UserPreference;
use App\Services\Nutrition\Interfaces\DailyNutritionServiceInterface;
use App\Services\Report\Interfaces\MonthlyReportServiceInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlyReportService implements MonthlyReportServiceInterface
{
    // Standard conversion: ~7700 calories = 1 kg weight change
    private const CALORIES_PER_KG = 7700.0;

    protected $dailyNutritionService;

    public function __construct(DailyNutritionServiceInterface $dailyNutritionService = null)
    {
        $this->dailyNutritionService = $dailyNutritionService;
    }

    /**
     * Get monthly summary of calories consumed and burned
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getMonthlyCaloriesSummary(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        try {
            // Calculate total calories consumed from food logs
            $totalCaloriesConsumed = $this->calculateTotalCaloriesConsumed($userId, $startDate, $endDate);

            // Calculate total calories burned from exercise logs
            $totalCaloriesBurned = $this->calculateTotalCaloriesBurned($userId, $startDate, $endDate);

            // Calculate net calories
            $netCalories = $totalCaloriesConsumed - $totalCaloriesBurned;

            // Get food log count
            $foodLogCount = UserFoodLog::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->count();

            // Get exercise log count
            $exerciseLogCount = UserExerciseLog::where('user_id', $userId)
                ->whereBetween('start_time', [$startDate, $endDate])
                ->count();

            // Format date range for response
            $monthName = $startDate->format('F Y');

            // Get user fitness goal
            $fitnessGoal = $this->getUserFitnessGoal($userId);

            // Get daily recommended calories (assuming 3390 if service not available)
            $dailyRecommendedCalories = 3390; // Default value

            if ($this->dailyNutritionService) {
                try {
                    $user = User::findOrFail($userId);
                    $dailyRecommendedCalories = $this->dailyNutritionService->getRecommendedCalories($user);

                    // Make sure $dailyRecommendedCalories is a float/int, not an array
                    if (is_array($dailyRecommendedCalories)) {
                        if (isset($dailyRecommendedCalories['calories'])) {
                            $dailyRecommendedCalories = (float) $dailyRecommendedCalories['calories'];
                        } else {
                            // Default to 3390 if we can't extract a numeric value
                            Log::warning('Recommended calories is an array without a "calories" key', ['data' => $dailyRecommendedCalories]);
                            $dailyRecommendedCalories = 3390;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not get recommended calories: ' . $e->getMessage());
                }
            }

            // Calculate monthly recommended calories
            $daysInMonth = $startDate->daysInMonth;
            $monthlyRecommendedCalories = (float) $dailyRecommendedCalories * $daysInMonth;

            // Calculate allowed calories including exercise calories burned
            // This adds burned calories to the recommendation for weight loss goals
            $adjustedRecommendedCalories = $monthlyRecommendedCalories + $totalCaloriesBurned;

            // Calculate calories deficit (negative means surplus)
            $caloriesDeficit = $adjustedRecommendedCalories - $totalCaloriesConsumed;

            // Calculate estimated weight change based on deficit/surplus
            // Negative deficit (surplus) means weight gain, positive deficit means weight loss
            $estimatedWeightChange = ($caloriesDeficit / self::CALORIES_PER_KG);

            // Calculate averages for days with data
            $daysWithData = max(1, $foodLogCount > 0 || $exerciseLogCount > 0 ?
                DB::table('user_food_logs')
                ->select(DB::raw('DATE(date) as log_date'))
                ->where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->union(
                    DB::table('user_exercise_logs')
                        ->select(DB::raw('DATE(start_time) as log_date'))
                        ->where('user_id', $userId)
                        ->whereBetween('start_time', [$startDate, $endDate])
                )
                ->distinct()
                ->count() : 1);

            // Today's date for time calculations
            $today = Carbon::now();

            // Calculate pro-rated recommended calories (for current month)
            $daysElapsed = ($today->year == $startDate->year && $today->month == $startDate->month)
                ? $today->day
                : $daysInMonth;

            $completionPercentage = ($daysElapsed / $daysInMonth) * 100;
            $proRatedRecommendedCalories = $dailyRecommendedCalories * $daysElapsed;
            $proRatedAdjustedRecommendedCalories = $proRatedRecommendedCalories + $totalCaloriesBurned;

            // Current deficit calculation
            $currentDeficit = $proRatedAdjustedRecommendedCalories - $totalCaloriesConsumed;
            $currentWeightChange = ($currentDeficit / self::CALORIES_PER_KG);

            // Projection calculations for incomplete months
            if ($daysElapsed < $daysInMonth) {
                // Project consumed calories for whole month
                $projectedCaloriesConsumed = ($totalCaloriesConsumed / $daysElapsed) * $daysInMonth;

                // Use actual burned calories for projection (more accurate than projection)
                $projectedCaloriesDeficit = ($adjustedRecommendedCalories - $projectedCaloriesConsumed);
                $projectedWeightChange = ($projectedCaloriesDeficit / self::CALORIES_PER_KG);
            } else {
                // For completed months, projected equals actual
                $projectedCaloriesDeficit = $caloriesDeficit;
                $projectedWeightChange = $estimatedWeightChange;
            }

            // Prepare goal progress with the estimated weight change
            $goalProgress = $this->calculateGoalProgress($fitnessGoal, $estimatedWeightChange, $totalCaloriesBurned);

            return [
                'month' => $monthName,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_calories_consumed' => round($totalCaloriesConsumed, 2),
                'total_calories_burned' => round($totalCaloriesBurned, 2),
                'net_calories' => round($netCalories, 2),
                'estimated_weight_change_kg' => round($estimatedWeightChange, 3),
                'daily_recommended_calories' => round((float) $dailyRecommendedCalories, 2),
                'monthly_recommended_calories' => round($monthlyRecommendedCalories, 2),
                'adjusted_recommended_calories' => round($adjustedRecommendedCalories, 2),
                'fitness_goal' => $fitnessGoal,
                'goal_progress' => $goalProgress,
                'food_log_count' => $foodLogCount,
                'exercise_log_count' => $exerciseLogCount,
                'days_elapsed' => $daysElapsed,
                'days_in_month' => $daysInMonth,
                'completion_percentage' => round($completionPercentage, 1),
                'days_with_data' => $daysWithData,
                'avg_daily_calories_consumed' => round($totalCaloriesConsumed / $daysWithData, 2),
                'avg_daily_calories_burned' => round($totalCaloriesBurned / $daysWithData, 2),
                'recommended_calories_to_date' => round($proRatedRecommendedCalories, 2),
                'adjusted_recommended_calories_to_date' => round($proRatedAdjustedRecommendedCalories, 2),
                'current_deficit' => round($currentDeficit, 2),
                'current_weight_change' => round($currentWeightChange, 3),
                'projected_deficit' => round($projectedCaloriesDeficit, 2),
                'projected_weight_change_kg' => round($projectedWeightChange, 3),
                'calories_deficit' => round($caloriesDeficit, 2)
            ];
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyCaloriesSummary: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'month' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_calories_consumed' => 0,
                'total_calories_burned' => 0,
                'net_calories' => 0,
                'estimated_weight_change_kg' => 0,
                'daily_recommended_calories' => 3390,
                'monthly_recommended_calories' => 3390 * $startDate->daysInMonth,
                'adjusted_recommended_calories' => 3390 * $startDate->daysInMonth,
                'fitness_goal' => 'Unknown',
                'goal_progress' => null,
                'food_log_count' => 0,
                'exercise_log_count' => 0,
                'days_elapsed' => 0,
                'days_in_month' => $startDate->daysInMonth,
                'completion_percentage' => 0,
                'days_with_data' => 0,
                'avg_daily_calories_consumed' => 0,
                'avg_daily_calories_burned' => 0,
                'recommended_calories_to_date' => 0,
                'adjusted_recommended_calories_to_date' => 0,
                'current_deficit' => 0,
                'current_weight_change' => 0,
                'projected_deficit' => 0,
                'projected_weight_change_kg' => 0,
                'calories_deficit' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get detailed daily calories data for the month
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getMonthlyCaloriesDetails(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        try {
            // Get all days in the month
            $period = CarbonPeriod::create($startDate, $endDate);

            // Get user fitness goal
            $fitnessGoal = $this->getUserFitnessGoal($userId);

            // Get daily recommended calories (assuming 3390 if service not available)
            $dailyRecommendedCalories = 3390; // Default value

            if ($this->dailyNutritionService) {
                try {
                    $user = User::findOrFail($userId);
                    $dailyRecommendedCalories = $this->dailyNutritionService->getRecommendedCalories($user);

                    // Make sure $dailyRecommendedCalories is a float/int, not an array
                    if (is_array($dailyRecommendedCalories)) {
                        if (isset($dailyRecommendedCalories['calories'])) {
                            $dailyRecommendedCalories = (float) $dailyRecommendedCalories['calories'];
                        } else {
                            // Default to 3390 if we can't extract a numeric value
                            Log::warning('Recommended calories is an array without a "calories" key', ['data' => $dailyRecommendedCalories]);
                            $dailyRecommendedCalories = 3390;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not get recommended calories: ' . $e->getMessage());
                }
            }

            // Calculate monthly recommended calories
            $daysInMonth = $startDate->daysInMonth;
            $monthlyRecommendedCalories = (float) $dailyRecommendedCalories * $daysInMonth;

            // Initialize array to hold daily data
            $dailyData = [];

            // Initialize data for each day in month
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $dailyData[$dateStr] = [
                    'date' => $dateStr,
                    'day' => $date->day,
                    'day_name' => $date->format('D'),
                    'calories_consumed' => 0,
                    'calories_burned' => 0,
                    'net_calories' => 0,
                    'recommended_calories' => (float) $dailyRecommendedCalories,
                    'adjusted_recommended_calories' => (float) $dailyRecommendedCalories,
                    'calories_deficit' => (float) $dailyRecommendedCalories,
                    'estimated_weight_change_kg' => 0,
                    'food_logs' => 0,
                    'exercise_logs' => 0
                ];
            }

            // Get and process food logs
            $foodLogs = $this->getFoodLogsWithCalories($userId, $startDate, $endDate);

            foreach ($foodLogs as $log) {
                $dateStr = Carbon::parse($log->date)->format('Y-m-d');

                if (isset($dailyData[$dateStr])) {
                    $dailyData[$dateStr]['calories_consumed'] += $log->calculated_calories;
                    $dailyData[$dateStr]['food_logs']++;
                }
            }

            // Get and process exercise logs
            $exerciseLogs = UserExerciseLog::where('user_id', $userId)
                ->whereBetween('start_time', [$startDate, $endDate])
                ->get();

            foreach ($exerciseLogs as $log) {
                $dateStr = Carbon::parse($log->start_time)->format('Y-m-d');

                if (isset($dailyData[$dateStr])) {
                    $dailyData[$dateStr]['calories_burned'] += $log->calories_burned;
                    $dailyData[$dateStr]['exercise_logs']++;
                }
            }

            // Calculate net calories and round values
            $totalCaloriesConsumed = 0;
            $totalCaloriesBurned = 0;

            foreach ($dailyData as &$day) {
                $day['net_calories'] = $day['calories_consumed'] - $day['calories_burned'];
                $day['adjusted_recommended_calories'] = $day['recommended_calories'] + $day['calories_burned'];
                $day['calories_deficit'] = $day['adjusted_recommended_calories'] - $day['calories_consumed'];

                // Calculate estimated weight change based on deficit/surplus
                // Positive deficit means weight loss, negative deficit (surplus) means weight gain
                $day['estimated_weight_change_kg'] = ($day['calories_deficit'] / self::CALORIES_PER_KG);

                // Round values
                $day['calories_consumed'] = round($day['calories_consumed'], 2);
                $day['calories_burned'] = round($day['calories_burned'], 2);
                $day['net_calories'] = round($day['net_calories'], 2);
                $day['recommended_calories'] = round($day['recommended_calories'], 2);
                $day['adjusted_recommended_calories'] = round($day['adjusted_recommended_calories'], 2);
                $day['calories_deficit'] = round($day['calories_deficit'], 2);
                $day['estimated_weight_change_kg'] = round($day['estimated_weight_change_kg'], 3);

                // Add to totals
                $totalCaloriesConsumed += $day['calories_consumed'];
                $totalCaloriesBurned += $day['calories_burned'];
            }

            // Calculate month totals
            $totalNetCalories = $totalCaloriesConsumed - $totalCaloriesBurned;
            $adjustedMonthlyRecommendedCalories = $monthlyRecommendedCalories + $totalCaloriesBurned;
            $caloriesDeficit = $adjustedMonthlyRecommendedCalories - $totalCaloriesConsumed;
            $estimatedWeightChange = ($caloriesDeficit / self::CALORIES_PER_KG);

            // Today's date for time calculations
            $today = Carbon::now();

            // Calculate pro-rated recommended calories (for current month)
            $daysElapsed = ($today->year == $startDate->year && $today->month == $startDate->month)
                ? $today->day
                : $daysInMonth;

            $completionPercentage = ($daysElapsed / $daysInMonth) * 100;
            $proRatedRecommendedCalories = $dailyRecommendedCalories * $daysElapsed;
            $proRatedAdjustedRecommendedCalories = $proRatedRecommendedCalories + $totalCaloriesBurned;

            // Current deficit calculation
            $currentDeficit = $proRatedAdjustedRecommendedCalories - $totalCaloriesConsumed;
            $currentWeightChange = ($currentDeficit / self::CALORIES_PER_KG);

            // Projection calculations for incomplete months
            if ($daysElapsed < $daysInMonth) {
                // Project consumed calories for whole month
                $projectedCaloriesConsumed = ($totalCaloriesConsumed / $daysElapsed) * $daysInMonth;

                // Use actual burned calories for projection (more accurate than projection)
                $projectedCaloriesDeficit = ($adjustedMonthlyRecommendedCalories - $projectedCaloriesConsumed);
                $projectedWeightChange = ($projectedCaloriesDeficit / self::CALORIES_PER_KG);
            } else {
                // For completed months, projected equals actual
                $projectedCaloriesDeficit = $caloriesDeficit;
                $projectedWeightChange = $estimatedWeightChange;
            }

            // Calculate days with data
            $daysWithData = count(array_filter($dailyData, function ($day) {
                return $day['food_logs'] > 0 || $day['exercise_logs'] > 0;
            }));
            $daysWithData = max(1, $daysWithData); // Avoid division by zero

            // Prepare goal progress with the estimated weight change
            $goalProgress = $this->calculateGoalProgress($fitnessGoal, $estimatedWeightChange, $totalCaloriesBurned);

            $monthTotals = [
                'total_calories_consumed' => round($totalCaloriesConsumed, 2),
                'total_calories_burned' => round($totalCaloriesBurned, 2),
                'total_net_calories' => round($totalNetCalories, 2),
                'daily_recommended_calories' => round((float) $dailyRecommendedCalories, 2),
                'monthly_recommended_calories' => round($monthlyRecommendedCalories, 2),
                'adjusted_monthly_recommended_calories' => round($adjustedMonthlyRecommendedCalories, 2),
                'calories_deficit' => round($caloriesDeficit, 2),
                'estimated_weight_change_kg' => round($estimatedWeightChange, 3),
                'fitness_goal' => $fitnessGoal,
                'goal_progress' => $goalProgress,
                'avg_daily_calories_consumed' => round($totalCaloriesConsumed / $daysWithData, 2),
                'avg_daily_calories_burned' => round($totalCaloriesBurned / $daysWithData, 2),
                'days_elapsed' => $daysElapsed,
                'days_with_data' => $daysWithData,
                'days_in_month' => $daysInMonth,
                'completion_percentage' => round($completionPercentage, 1),
                'recommended_calories_to_date' => round($proRatedRecommendedCalories, 2),
                'adjusted_recommended_calories_to_date' => round($proRatedAdjustedRecommendedCalories, 2),
                'current_deficit' => round($currentDeficit, 2),
                'current_weight_change' => round($currentWeightChange, 3),
                'projected_deficit' => round($projectedCaloriesDeficit, 2),
                'projected_weight_change_kg' => round($projectedWeightChange, 3)
            ];

            return [
                'month' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'daily_data' => array_values($dailyData),
                'month_totals' => $monthTotals
            ];
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyCaloriesDetails: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'month' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'daily_data' => [],
                'month_totals' => [
                    'total_calories_consumed' => 0,
                    'total_calories_burned' => 0,
                    'total_net_calories' => 0,
                    'daily_recommended_calories' => 3390,
                    'monthly_recommended_calories' => 3390 * $startDate->daysInMonth,
                    'adjusted_monthly_recommended_calories' => 3390 * $startDate->daysInMonth,
                    'calories_deficit' => 0,
                    'estimated_weight_change_kg' => 0,
                    'fitness_goal' => 'Unknown',
                    'goal_progress' => null,
                    'avg_daily_calories_consumed' => 0,
                    'avg_daily_calories_burned' => 0,
                    'days_elapsed' => 0,
                    'days_with_data' => 0,
                    'days_in_month' => $startDate->daysInMonth,
                    'completion_percentage' => 0,
                    'recommended_calories_to_date' => 0,
                    'adjusted_recommended_calories_to_date' => 0,
                    'current_deficit' => 0,
                    'current_weight_change' => 0,
                    'projected_deficit' => 0,
                    'projected_weight_change_kg' => 0
                ],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get user's fitness goal from preferences
     *
     * @param int $userId
     * @return string
     */
    private function getUserFitnessGoal(int $userId): string
    {
        try {
            $preference = UserPreference::where('user_id', $userId)->first();

            if ($preference && !empty($preference->fitness_goals)) {
                return $preference->fitness_goals;
            }

            return 'Maintenance'; // Default goal if none specified
        } catch (\Exception $e) {
            Log::warning('Error getting user fitness goal: ' . $e->getMessage());
            return 'Maintenance';
        }
    }

    /**
     * Calculate progress towards fitness goal
     *
     * @param string $fitnessGoal
     * @param float $estimatedWeightChange
     * @param float $totalCaloriesBurned
     * @return array
     */
    private function calculateGoalProgress(string $fitnessGoal, float $estimatedWeightChange, float $totalCaloriesBurned): array
    {
        $progress = [
            'status' => null,
            'message' => null,
            'percentage' => null,
            'target_achieved' => false
        ];

        // Simplify the goal to handle different formatting
        $simplifiedGoal = strtolower(trim($fitnessGoal));

        if (strpos($simplifiedGoal, 'weight loss') !== false) {
            // For weight loss goal (positive weight change is good)
            if ($estimatedWeightChange > 0) {
                // Target: Aim for 4kg per month (max healthy weight loss)
                $target = 4.0;
                $percentage = min(100, ($estimatedWeightChange / $target) * 100);

                $progress['status'] = 'On Track';
                $progress['message'] = 'You are making progress toward your weight loss goal';
                $progress['percentage'] = round($percentage, 1);
                $progress['target_achieved'] = $estimatedWeightChange >= $target;
            } else {
                $progress['status'] = 'Off Track';
                $progress['message'] = 'You need a calorie deficit to achieve weight loss';
                $progress['percentage'] = 0;
                $progress['target_achieved'] = false;
            }
        } elseif (strpos($simplifiedGoal, 'weight gain') !== false || strpos($simplifiedGoal, 'muscle') !== false) {
            // For weight/muscle gain goal (negative weight change is good)
            if ($estimatedWeightChange < 0) {
                // Target: Aim for 2kg per month (healthy muscle gain)
                $target = -2.0;
                $percentage = min(100, (abs($estimatedWeightChange) / abs($target)) * 100);

                $progress['status'] = 'On Track';
                $progress['message'] = 'You are making progress toward your muscle/weight gain goal';
                $progress['percentage'] = round($percentage, 1);
                $progress['target_achieved'] = $estimatedWeightChange <= $target;
            } else {
                $progress['status'] = 'Off Track';
                $progress['message'] = 'You need a calorie surplus to achieve weight/muscle gain';
                $progress['percentage'] = 0;
                $progress['target_achieved'] = false;
            }
        } elseif (strpos($simplifiedGoal, 'maintenance') !== false) {
            // For weight maintenance goal (minimal weight change is good)
            if (abs($estimatedWeightChange) < 0.5) {
                $progress['status'] = 'On Track';
                $progress['message'] = 'You are maintaining your weight successfully';
                $progress['percentage'] = 100;
                $progress['target_achieved'] = true;
            } else {
                // Further from maintenance
                $progress['status'] = 'Off Track';
                $progress['message'] = 'Your calorie balance is leading to weight ' .
                    ($estimatedWeightChange < 0 ? 'gain' : 'loss');
                $progress['percentage'] = max(0, 100 - (abs($estimatedWeightChange) - 0.5) * 50);
                $progress['target_achieved'] = false;
            }
        } elseif (strpos($simplifiedGoal, 'fitness') !== false || strpos($simplifiedGoal, 'endurance') !== false) {
            // For general fitness/endurance goal (more calories burned is better)
            // Target: 10,000 calories burned in exercise per month (arbitrary target)
            $target = 10000;
            $percentage = min(100, ($totalCaloriesBurned / $target) * 100);

            $progress['status'] = $percentage >= 50 ? 'On Track' : 'Off Track';
            $progress['message'] = $percentage >= 50
                ? 'You are making good progress on your fitness goal'
                : 'More regular exercise would help achieve your fitness goal';
            $progress['percentage'] = round($percentage, 1);
            $progress['target_achieved'] = $percentage >= 100;
        } else {
            // Default for unknown goals
            $progress['status'] = 'Unknown Goal';
            $progress['message'] = 'Set a specific fitness goal in your preferences';
            $progress['percentage'] = null;
            $progress['target_achieved'] = false;
        }

        return $progress;
    }

    /**
     * Calculate total calories consumed
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    private function calculateTotalCaloriesConsumed(int $userId, Carbon $startDate, Carbon $endDate): float
    {
        $foodLogs = $this->getFoodLogsWithCalories($userId, $startDate, $endDate);
        return $foodLogs->sum('calculated_calories');
    }

    /**
     * Get food logs with calculated calories
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFoodLogsWithCalories(int $userId, Carbon $startDate, Carbon $endDate)
    {
        // Get food logs in the date range
        $foodLogs = UserFoodLog::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['foodItem.foodNutrition.nutritionType'])
            ->get();

        // Calculate calories for each food log
        foreach ($foodLogs as $log) {
            $calculatedCalories = 0;

            if ($log->foodItem && $log->foodItem->foodNutrition) {
                foreach ($log->foodItem->foodNutrition as $nutrition) {
                    if (
                        $nutrition->nutritionType &&
                        (stripos($nutrition->nutritionType->name, 'calorie') !== false ||
                            stripos($nutrition->nutritionType->name, 'energy') !== false)
                    ) {

                        // Calculate serving ratio based on weight_per_serving if available
                        if ($log->foodItem->weight_per_serving) {
                            $servingInGrams = $log->foodItem->weight_per_serving * $log->serving_size;
                            $servingRatio = $servingInGrams / 100;
                        } else {
                            $servingRatio = $log->serving_size / 100;
                        }

                        $calculatedCalories += $nutrition->amount_per_100g * $servingRatio;
                    }
                }
            }

            $log->calculated_calories = $calculatedCalories;
        }

        return $foodLogs;
    }

    /**
     * Calculate total calories burned
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    private function calculateTotalCaloriesBurned(int $userId, Carbon $startDate, Carbon $endDate): float
    {
        return UserExerciseLog::where('user_id', $userId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->sum('calories_burned');
    }
}
