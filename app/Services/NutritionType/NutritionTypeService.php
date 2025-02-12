<?php
// app/Services/NutritionType/NutritionTypeService.php
namespace App\Services\NutritionType;

use App\Services\NutritionType\Interfaces\NutritionTypeServiceInterface;
use App\Repositories\NutritionType\Interfaces\NutritionTypeRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NutritionTypeService implements NutritionTypeServiceInterface
{
    protected $nutritionTypeRepository;

    public function __construct(NutritionTypeRepositoryInterface $nutritionTypeRepository)
    {
        $this->nutritionTypeRepository = $nutritionTypeRepository;
    }

    public function getAllNutritionTypes()
    {
        try {
            return $this->nutritionTypeRepository->getAllActive();
        } catch (\Exception $e) {
            Log::error('Error fetching nutrition types: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getNutritionType($nutritionId)
    {
        try {
            return $this->nutritionTypeRepository->getById($nutritionId);
        } catch (\Exception $e) {
            Log::error('Error fetching nutrition type: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createNutritionType(array $data)
    {
        try {
            DB::beginTransaction();

            // Check if nutrition type with same name exists
            if ($this->nutritionTypeRepository->findByName($data['name'])) {
                throw new \Exception('Nutrition type with this name already exists');
            }

            $nutritionType = $this->nutritionTypeRepository->create($data);

            DB::commit();
            return $nutritionType;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating nutrition type: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateNutritionType($nutritionId, array $data)
    {
        try {
            DB::beginTransaction();

            // Check if name is being updated and if it conflicts
            if (isset($data['name'])) {
                $existing = $this->nutritionTypeRepository->findByName($data['name']);
                if ($existing && $existing->nutrition_id !== $nutritionId) {
                    throw new \Exception('Nutrition type with this name already exists');
                }
            }

            $nutritionType = $this->nutritionTypeRepository->update($nutritionId, $data);

            DB::commit();
            return $nutritionType;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating nutrition type: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteNutritionType($nutritionId)
    {
        try {
            DB::beginTransaction();

            $result = $this->nutritionTypeRepository->delete($nutritionId);

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting nutrition type: ' . $e->getMessage());
            throw $e;
        }
    }
}
