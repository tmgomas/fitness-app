<?php
// app/Services/UserExerciseLogService.php
namespace App\Services;

use App\Models\UserExerciseLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserExerciseLogService
{
    public function getFilteredLogs(Request $request)
    {
        $query = UserExerciseLog::query()
            ->where('user_id', $request->user()->id)
            ->with(['exercise']);

        if ($request->has('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('end_time', '<=', $request->end_date);
        }

        if ($request->has('exercise_id')) {
            $query->where('exercise_id', $request->exercise_id);
        }

        return $query->latest()->paginate($request->per_page ?? 15);
    }

    public function createLog(array $data)
    {
        $data['duration_minutes'] = $this->calculateDuration($data['start_time'], $data['end_time']);
        $data['calories_burned'] = $this->calculateCalories($data);

        return UserExerciseLog::create($data);
    }

    public function updateLog(UserExerciseLog $log, array $data)
    {
        if (isset($data['start_time']) || isset($data['end_time'])) {
            $data['duration_minutes'] = $this->calculateDuration(
                $data['start_time'] ?? $log->start_time,
                $data['end_time'] ?? $log->end_time
            );
        }

        $log->update($data);
        return $log->fresh();
    }

    public function deleteLog(UserExerciseLog $log)
    {
        return $log->delete();
    }

    public function getUserStats($user)
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            'today' => $this->calculateStats($user->id, $today),
            'this_week' => $this->calculateStats($user->id, $startOfWeek),
            'this_month' => $this->calculateStats($user->id, $startOfMonth),
        ];
    }

    protected function calculateStats($userId, $startDate)
    {
        return UserExerciseLog::where('user_id', $userId)
            ->whereDate('start_time', '>=', $startDate)
            ->selectRaw('
                COUNT(*) as total_workouts,
                SUM(duration_minutes) as total_duration,
                SUM(calories_burned) as total_calories,
                AVG(avg_heart_rate) as average_heart_rate
            ')
            ->first();
    }

    protected function calculateDuration($startTime, $endTime)
    {
        return Carbon::parse($startTime)->diffInMinutes(Carbon::parse($endTime));
    }

    protected function calculateCalories($data)
    {
        // Implement your calorie calculation logic here
        // This is a simplified example
        $baseCalories = $data['duration_minutes'] * 5; // 5 calories per minute as base
        $intensityMultiplier = $this->getIntensityMultiplier($data['intensity_level']);
        return $baseCalories * $intensityMultiplier;
    }

    protected function getIntensityMultiplier($intensity)
    {
        return match ($intensity) {
            'low' => 0.8,
            'medium' => 1.0,
            'high' => 1.2,
            default => 1.0,
        };
    }
}
