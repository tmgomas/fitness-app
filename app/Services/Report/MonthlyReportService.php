<?php

namespace App\Services\Report;

use App\Models\UserExerciseLog;
use App\Models\UserFoodLog;
use App\Services\Report\Interfaces\MonthlyReportServiceInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlyReportService implements MonthlyReportServiceInterface
{
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

            return [
                'month' => $monthName,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_calories_consumed' => round($totalCaloriesConsumed, 2),
                'total_calories_burned' => round($totalCaloriesBurned, 2),
                'net_calories' => round($totalCaloriesConsumed - $totalCaloriesBurned, 2),
                'food_log_count' => $foodLogCount,
                'exercise_log_count' => $exerciseLogCount
            ];
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyCaloriesSummary: ' . $e->getMessage());
            return [
                'month' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_calories_consumed' => 0,
                'total_calories_burned' => 0,
                'net_calories' => 0,
                'food_log_count' => 0,
                'exercise_log_count' => 0,
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

                // Round values
                $day['calories_consumed'] = round($day['calories_consumed'], 2);
                $day['calories_burned'] = round($day['calories_burned'], 2);
                $day['net_calories'] = round($day['net_calories'], 2);

                // Add to totals
                $totalCaloriesConsumed += $day['calories_consumed'];
                $totalCaloriesBurned += $day['calories_burned'];
            }

            // Calculate month totals
            $monthTotals = [
                'total_calories_consumed' => round($totalCaloriesConsumed, 2),
                'total_calories_burned' => round($totalCaloriesBurned, 2),
                'total_net_calories' => round($totalCaloriesConsumed - $totalCaloriesBurned, 2),
                'avg_daily_calories_consumed' => count($dailyData) > 0 ? round($totalCaloriesConsumed / count($dailyData), 2) : 0,
                'avg_daily_calories_burned' => count($dailyData) > 0 ? round($totalCaloriesBurned / count($dailyData), 2) : 0
            ];

            return [
                'month' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'daily_data' => array_values($dailyData),
                'month_totals' => $monthTotals
            ];
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyCaloriesDetails: ' . $e->getMessage());
            return [
                'month' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'daily_data' => [],
                'month_totals' => [
                    'total_calories_consumed' => 0,
                    'total_calories_burned' => 0,
                    'total_net_calories' => 0,
                    'avg_daily_calories_consumed' => 0,
                    'avg_daily_calories_burned' => 0
                ],
                'error' => $e->getMessage()
            ];
        }
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
