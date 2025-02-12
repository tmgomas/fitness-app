<?php
// app/Services/ExerciseCategory/Interfaces/ExerciseCategoryServiceInterface.php
namespace App\Services\ExerciseCategory\Interfaces;

interface ExerciseCategoryServiceInterface
{
    public function getAllCategories();
    public function getCategory($id);
    public function createCategory(array $data);
    public function updateCategory($id, array $data);
    public function deleteCategory($id);
}
