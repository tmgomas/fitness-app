<?php

namespace App\Repositories\UserFoodLog;

use App\Models\UserFoodLog;
use App\Repositories\UserFoodLog\Interfaces\UserFoodLogRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserFoodLogRepository implements UserFoodLogRepositoryInterface
{
    protected $model;

    public function __construct(UserFoodLog $model)
    {
        $this->model = $model;
    }

    public function getAllForUser($userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['foodItem.foodNutrition.nutritionType'])
            ->get();
    }

    public function createForUser($userId, array $data)
    {
        $data['user_id'] = $userId;
        $data['food_log_id'] = (string) Str::uuid();
        return $this->model->create($data);
    }

    public function findForUser($userId, $foodLogId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('food_log_id', $foodLogId)
            ->with(['foodItem.foodNutrition.nutritionType'])
            ->firstOrFail();
    }

    public function updateForUser($userId, $foodLogId, array $data)
    {
        $foodLog = $this->findForUser($userId, $foodLogId);
        $foodLog->update($data);
        return $foodLog->fresh();
    }

    public function deleteForUser($userId, $foodLogId)
    {
        $foodLog = $this->findForUser($userId, $foodLogId);
        return $foodLog->delete();
    }

public function getFoodLogsWithFilters($userId, array $filters)
{
    $query = $this->model->where('user_id', $userId);

    if (isset($filters['start_date']) && isset($filters['end_date'])) {
        $startDate = Carbon::parse($filters['start_date'])->startOfDay();
        $endDate = Carbon::parse($filters['end_date'])->endOfDay();
        
        // Use strict date comparison
        $query->whereDate('date', '>=', $startDate->toDateString())
              ->whereDate('date', '<=', $endDate->toDateString());
              
        // Add debug log
        \Illuminate\Support\Facades\Log::info('Date filter applied', [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'start_datetime' => $startDate->toDateTimeString(),
            'end_datetime' => $endDate->toDateTimeString()
        ]);
    }

    $query->with(['foodItem.foodNutrition.nutritionType']);
    
    $results = $query->get();
    
    // Add debug log
    \Illuminate\Support\Facades\Log::info('Food logs retrieved', [
        'count' => $results->count(),
        'dates' => $results->pluck('date')->toArray()
    ]);
    
    return $results;
}
}
