<?php

namespace App\Repositories\UserExerciseLog;

use App\Models\UserExerciseLog;
use App\Repositories\UserExerciseLog\Interfaces\UserExerciseLogRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserExerciseLogRepository implements UserExerciseLogRepositoryInterface
{
    protected $model;

    public function __construct(UserExerciseLog $model)
    {
        $this->model = $model;
    }

    public function getAllForUser($userId, array $filters = [])
    {
        $query = $this->model
            ->where('user_id', $userId)
            ->with(['exercise', 'customExercise']);

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('start_time', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay()
            ]);
        }

        if (isset($filters['exercise_id'])) {
            $query->where('exercise_id', $filters['exercise_id']);
        }

        return $query->get();
    }

    public function createForUser($userId, array $data)
    {
        $data['user_id'] = $userId;
        return $this->model->create($data);
    }

    public function findForUser($userId, $logId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('id', $logId)  // or whatever your primary key is
            ->with(['exercise', 'customExercise'])
            ->firstOrFail();
    }

    public function updateForUser($userId, $logId, array $data)
    {
        $exerciseLog = $this->findForUser($userId, $logId);
        $exerciseLog->update($data);
        return $exerciseLog->fresh();
    }

    public function deleteForUser($userId, $logId)
    {
        $exerciseLog = $this->findForUser($userId, $logId);
        return $exerciseLog->delete();
    }

    public function getStatsForUser($userId, array $dateRange)
    {
        $stats = $this->model
            ->where('user_id', $userId)
            ->whereBetween('start_time', [
                Carbon::parse($dateRange['start_date'])->startOfDay(),
                Carbon::parse($dateRange['end_date'])->endOfDay()
            ])
            ->selectRaw('
                COUNT(*) as total_workouts,
                SUM(duration_minutes) as total_duration,
                SUM(calories_burned) as total_calories,
                AVG(avg_heart_rate) as average_heart_rate,
                COUNT(DISTINCT DATE(start_time)) as active_days
            ')
            ->first();

        $mostCommonExercise = $this->model
            ->where('user_id', $userId)
            ->whereBetween('start_time', [
                Carbon::parse($dateRange['start_date'])->startOfDay(),
                Carbon::parse($dateRange['end_date'])->endOfDay()
            ])
            ->with('exercise')
            ->select('exercise_id', DB::raw('count(*) as count'))
            ->groupBy('exercise_id')
            ->orderByDesc('count')
            ->first();

        return [
            'stats' => $stats,
            'most_common_exercise' => $mostCommonExercise
        ];
    }
}
