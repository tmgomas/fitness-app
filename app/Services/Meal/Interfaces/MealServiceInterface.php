<?php
// app/Services/Meal/Interfaces/MealServiceInterface.php
namespace App\Services\Meal\Interfaces;

interface MealServiceInterface
{
    public function getAllMeals(int $perPage = 10);
    public function getMeal($id);
    public function createMeal(array $data);
    public function updateMeal($id, array $data);
    public function deleteMeal($id);
    public function searchMeals(string $query);
    public function handleMealImage($image, $oldImageUrl = null);
}
