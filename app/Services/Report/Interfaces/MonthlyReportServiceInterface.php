<?php

namespace App\Services\Report\Interfaces;

use Carbon\Carbon;

interface MonthlyReportServiceInterface
{
    /**
     * Get monthly summary of calories consumed and burned
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getMonthlyCaloriesSummary(int $userId, Carbon $startDate, Carbon $endDate): array;

    /**
     * Get detailed daily calories data for the month
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getMonthlyCaloriesDetails(int $userId, Carbon $startDate, Carbon $endDate): array;
}
