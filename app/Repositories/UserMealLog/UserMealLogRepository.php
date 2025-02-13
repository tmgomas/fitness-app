<?php

namespace App\Repositories\UserMealLog;

use App\Models\UserMealLog;
use App\Repositories\UserMealLog\Interfaces\UserMealLogRepositoryInterface;
use Carbon\Carbon;

class UserMealLogRepository implements UserMealLogRepositoryInterface
{
    protected $model;

    public function __construct(UserMealLog $model)
    {
        $this->model = $model;
    }

    public function getAllForUser($userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->with([
                'meal',
                'meal.nutritionFacts.nutritionType',
                'meal.foods.foodItem.foodNutrition.nutritionType'
            ])
            ->get();
    }

    public function createForUser($userId, array $data)
    {
        $data['user_id'] = $userId;
        return $this->model->create($data);
    }

    public function findForUser($userId, $mealLogId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('meal_log_id', $mealLogId)
            ->with([
                'meal',
                'meal.nutritionFacts.nutritionType',
                'meal.foods.foodItem.foodNutrition.nutritionType'
            ])
            ->firstOrFail();
    }

    public function updateForUser($userId, $mealLogId, array $data)
    {
        $mealLog = $this->findForUser($userId, $mealLogId);
        $mealLog->update($data);
        return $mealLog->fresh();
    }

    public function deleteForUser($userId, $mealLogId)
    {
        $mealLog = $this->findForUser($userId, $mealLogId);
        return $mealLog->delete();
    }

    public function getMealLogsWithFilters($userId, array $filters)
    {
        $query = $this->model->where('user_id', $userId);

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $startDate = Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $query->with([
            'meal',
            'meal.nutritionFacts.nutritionType',
            'meal.foods.foodItem.foodNutrition.nutritionType'
        ]);

        return $query->get();
    }
}
