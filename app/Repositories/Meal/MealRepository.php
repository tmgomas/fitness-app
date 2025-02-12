<?php
// app/Repositories/Meal/MealRepository.php
namespace App\Repositories\Meal;

use App\Models\Meal;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Meal\Interfaces\MealRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class MealRepository extends BaseRepository implements MealRepositoryInterface
{
    public function __construct(Meal $model)
    {
        parent::__construct($model);
    }

    public function getWithRelations($id)
    {
        return $this->model->with(['nutritionFacts', 'foods'])->find($id);
    }

    public function searchMeals(string $query, bool $isActive = true)
    {
        return $this->model->where('is_active', $isActive)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->with(['nutritionFacts', 'foods'])
            ->latest()
            ->paginate(10);
    }

    public function createWithRelations(array $data)
    {
        $meal = $this->model->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'image_url' => $data['image_url'] ?? null,
            'default_serving_size' => $data['default_serving_size'],
            'serving_unit' => $data['serving_unit'],
            'is_active' => $data['is_active'] ?? true
        ]);

        if (isset($data['nutrition_facts'])) {
            foreach ($data['nutrition_facts'] as $nutrition) {
                $meal->nutritionFacts()->create($nutrition);
            }
        }

        if (isset($data['foods'])) {
            foreach ($data['foods'] as $food) {
                $meal->foods()->create($food);
            }
        }

        return $meal->load(['nutritionFacts', 'foods']);
    }

    public function updateWithRelations($id, array $data)
    {
        $meal = $this->find($id);

        $meal->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'image_url' => $data['image_url'] ?? $meal->image_url,
            'default_serving_size' => $data['default_serving_size'],
            'serving_unit' => $data['serving_unit'],
            'is_active' => $data['is_active'] ?? $meal->is_active
        ]);

        if (isset($data['nutrition_facts'])) {
            $meal->nutritionFacts()->delete();
            foreach ($data['nutrition_facts'] as $nutrition) {
                $meal->nutritionFacts()->create($nutrition);
            }
        }

        if (isset($data['foods'])) {
            $meal->foods()->delete();
            foreach ($data['foods'] as $food) {
                $meal->foods()->create($food);
            }
        }

        return $meal->load(['nutritionFacts', 'foods']);
    }
}
