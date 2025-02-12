<?php
// app/Repositories/ExerciseCategory/Interfaces/ExerciseCategoryRepositoryInterface.php
namespace App\Repositories\ExerciseCategory\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface ExerciseCategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getWithExercises($id);
    public function getAllWithExercises();
}
