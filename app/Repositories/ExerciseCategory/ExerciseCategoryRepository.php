<?php
// app/Repositories/ExerciseCategory/ExerciseCategoryRepository.php
namespace App\Repositories\ExerciseCategory;

use App\Models\ExerciseCategory;
use App\Repositories\Base\BaseRepository;
use App\Repositories\ExerciseCategory\Interfaces\ExerciseCategoryRepositoryInterface;

class ExerciseCategoryRepository extends BaseRepository implements ExerciseCategoryRepositoryInterface
{
    public function __construct(ExerciseCategory $model)
    {
        parent::__construct($model);
    }

    public function getWithExercises($id)
    {
        return $this->model->with('exercises')->find($id);
    }

    public function getAllWithExercises()
    {
        return $this->model->with('exercises')->get();
    }
}
