<?php
// app/Services/ExerciseCategory/ExerciseCategoryService.php
namespace App\Services\ExerciseCategory;

use App\Repositories\ExerciseCategory\Interfaces\ExerciseCategoryRepositoryInterface;
use App\Services\ExerciseCategory\Interfaces\ExerciseCategoryServiceInterface;
use Illuminate\Support\Facades\DB;

class ExerciseCategoryService implements ExerciseCategoryServiceInterface
{
    protected $categoryRepository;

    public function __construct(ExerciseCategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        // Change from get() to paginate()
        return $this->categoryRepository->query()
            ->with('exercises')
            ->latest()
            ->paginate(10);
    }

    public function getCategory($id)
    {
        return $this->categoryRepository->getWithExercises($id);
    }

    public function createCategory(array $data)
    {
        try {
            DB::beginTransaction();
            $category = $this->categoryRepository->create($data);
            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateCategory($id, array $data)
    {
        try {
            DB::beginTransaction();
            $category = $this->categoryRepository->update($id, $data);
            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteCategory($id)
    {
        try {
            DB::beginTransaction();
            $result = $this->categoryRepository->delete($id);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
