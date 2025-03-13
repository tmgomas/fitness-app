<?php

namespace App\Services\CustomExercise;

use App\Repositories\CustomExercise\Interfaces\CustomExerciseRepositoryInterface;
use App\Services\CustomExercise\Interfaces\CustomExerciseServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomExerciseService implements CustomExerciseServiceInterface
{
    protected $customExerciseRepository;

    public function __construct(CustomExerciseRepositoryInterface $customExerciseRepository)
    {
        $this->customExerciseRepository = $customExerciseRepository;
    }

    public function getAllCustomExercises()
    {
        try {
            return $this->customExerciseRepository->getAllByUserId(Auth::id());
        } catch (\Exception $e) {
            Log::error('Error fetching custom exercises: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getCustomExercise(string $id)
    {
        try {
            return $this->customExerciseRepository->findByIdAndUser($id, Auth::id());
        } catch (\Exception $e) {
            Log::error('Error fetching custom exercise: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createCustomExercise(array $data)
    {
        try {
            $data['user_id'] = Auth::id();
            return $this->customExerciseRepository->create($data);
        } catch (\Exception $e) {
            Log::error('Error creating custom exercise: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateCustomExercise(string $id, array $data)
    {
        try {
            // Ensure user can only update their own exercises
            $exercise = $this->getCustomExercise($id);
            return $this->customExerciseRepository->update($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating custom exercise: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteCustomExercise(string $id)
    {
        try {
            // Ensure user can only delete their own exercises
            $exercise = $this->getCustomExercise($id);
            return $this->customExerciseRepository->delete($id);
        } catch (\Exception $e) {
            Log::error('Error deleting custom exercise: ' . $e->getMessage());
            throw $e;
        }
    }
}
