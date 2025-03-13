<?php

namespace App\Repositories\CustomExercise;

use App\Models\UserCustomExercise;
use App\Repositories\Base\BaseRepository;
use App\Repositories\CustomExercise\Interfaces\CustomExerciseRepositoryInterface;

class CustomExerciseRepository extends BaseRepository implements CustomExerciseRepositoryInterface
{
    public function __construct(UserCustomExercise $model)
    {
        parent::__construct($model);
    }

    public function getAllByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->latest()->get();
    }

    public function findByIdAndUser(string $id, int $userId)
    {
        return $this->model->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }
}
