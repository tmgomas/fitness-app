<?php

namespace App\Services\Nutrition\Interfaces;

use App\Models\User;

interface DailyNutritionServiceInterface
{
    /**
     * Get a complete nutrition summary for a user on a given date
     *
     * @param User $user
     * @param string|null $date
     * @return array
     */
    public function getNutritionSummary(User $user, ?string $date = null): array;

    /**
     * Get recommended daily calories for a user based on their health data and preferences
     *
     * @param User $user
     * @return array
     */
    public function getRecommendedCalories(User $user): array;

    /**
     * Get total consumed calories for a user on a given date
     *
     * @param User $user
     * @param string|null $date
     * @return float
     */
    public function getConsumedCalories(User $user, ?string $date = null): float;

    /**
     * Get total burned calories through exercise for a user on a given date
     *
     * @param User $user
     * @param string|null $date
     * @return float
     */
    public function getBurnedCalories(User $user, ?string $date = null): float;
}
