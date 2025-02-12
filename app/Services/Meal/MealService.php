<?php
// app/Services/Meal/MealService.php
namespace App\Services\Meal;

use App\Repositories\Meal\Interfaces\MealRepositoryInterface;
use App\Services\Meal\Interfaces\MealServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MealService implements MealServiceInterface
{
    protected $mealRepository;

    public function __construct(MealRepositoryInterface $mealRepository)
    {
        $this->mealRepository = $mealRepository;
    }

    public function getAllMeals(int $perPage = 10)
    {
        return $this->mealRepository->query()
            ->with(['nutritionFacts', 'foods'])
            ->latest()
            ->paginate($perPage);
    }

    public function getMeal($id)
    {
        return $this->mealRepository->getWithRelations($id);
    }

    public function createMeal(array $data)
    {
        try {
            DB::beginTransaction();

            if (isset($data['image'])) {
                $data['image_url'] = $this->handleMealImage($data['image']);
                unset($data['image']);
            }

            $meal = $this->mealRepository->createWithRelations($data);

            DB::commit();
            return $meal;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateMeal($id, array $data)
    {
        try {
            DB::beginTransaction();

            $meal = $this->mealRepository->find($id);

            if (isset($data['image'])) {
                $data['image_url'] = $this->handleMealImage($data['image'], $meal->image_url);
                unset($data['image']);
            }

            $meal = $this->mealRepository->updateWithRelations($id, $data);

            DB::commit();
            return $meal;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteMeal($id)
    {
        try {
            DB::beginTransaction();

            $meal = $this->mealRepository->find($id);
            if ($meal->image_url) {
                $this->deleteImage($meal->image_url);
            }

            $this->mealRepository->delete($id);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function searchMeals(string $query)
    {
        return $this->mealRepository->searchMeals($query);
    }

    public function handleMealImage($image, $oldImageUrl = null)
    {
        if ($oldImageUrl) {
            $this->deleteImage($oldImageUrl);
        }

        $path = $image->store('meals', 'public');
        return Storage::url($path);
    }

    protected function deleteImage($imageUrl)
    {
        Storage::delete(str_replace('/storage/', '', $imageUrl));
    }
}
