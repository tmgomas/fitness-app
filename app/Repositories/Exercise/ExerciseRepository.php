<?php

namespace App\Repositories\Exercise;

use App\Models\Exercise;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Exercise\Interfaces\ExerciseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ExerciseRepository extends BaseRepository implements ExerciseRepositoryInterface
{
    public function __construct(Exercise $model)
    {
        parent::__construct($model);
    }

    public function getAllWithPagination(int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->with(['category'])
            ->latest()
            ->paginate($perPage);
    }

    public function search(string $query): LengthAwarePaginator
    {
        return $this->query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('category')
            ->latest()
            ->paginate(10);
    }

    public function getByCategory(string $categoryId): LengthAwarePaginator
    {
        return $this->query()
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->with('category')
            ->latest()
            ->paginate(10);
    }

    // Override update method with logging
    public function update($id, array $data)
    {
        try {
           Log::info('Exercise Repository - Update Start', [
                'exercise_id' => $id,
                'data' => $data
            ]);

            $exercise = $this->find($id);
            $exercise->update($data);
            $updated = $exercise->fresh();

           Log::info('Exercise Updated Successfully', [
                'exercise' => $updated
            ]);

            return $updated;
        } catch (\Exception $e) {
           Log::error('Exercise Repository Update Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
