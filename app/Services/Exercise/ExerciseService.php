<?php

namespace App\Services\Exercise;

use App\Models\Exercise;
use App\Repositories\Exercise\Interfaces\ExerciseRepositoryInterface;
use App\Services\Exercise\Interfaces\ExerciseServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExerciseService implements ExerciseServiceInterface
{
    private ExerciseRepositoryInterface $exerciseRepository;

    public function __construct(ExerciseRepositoryInterface $exerciseRepository)
    {
        $this->exerciseRepository = $exerciseRepository;
    }

    public function getAllExercises(): LengthAwarePaginator
    {
        return $this->exerciseRepository->getAllWithPagination();
    }

    public function createExercise(array $data, ?UploadedFile $image = null): Exercise
    {
        if ($image) {
            $imagePath = $image->store('exercises', 'public');
            $data['image_url'] = Storage::url($imagePath);
        }

        return $this->exerciseRepository->create($data);
    }

    public function updateExercise(string $id, array $data, ?UploadedFile $image = null): Exercise
    {
        try {
           Log::info('Exercise Service - Update Start', [
                'exercise_id' => $id,
                'data' => $data,
                'has_image' => !is_null($image)
            ]);

            // Make sure we're working with just the ID string
            if ($id instanceof Exercise) {
                $id = $id->id;
            } elseif (is_array($id) && isset($id['id'])) {
                $id = $id['id'];
            } elseif (is_object($id) && isset($id->id)) {
                $id = $id->id;
            }

           Log::info('Processed ID', ['id' => $id]);

            $exercise = $this->exerciseRepository->find($id);

            if ($image) {
                if ($exercise->image_url) {
                   Log::info('Deleting Old Image', ['old_image' => $exercise->image_url]);
                    Storage::delete(str_replace('/storage/', '', $exercise->image_url));
                }

                $imagePath = $image->store('exercises', 'public');
               Log::info('New Image Stored', ['path' => $imagePath]);
                $data['image_url'] = Storage::url($imagePath);
            }

            $updated = $this->exerciseRepository->update($id, $data);
           Log::info('Exercise Repository Update Complete', ['updated' => $updated]);

            return $updated;
        } catch (\Exception $e) {
           Log::error('Exercise Service Update Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id_type' => gettype($id),
                'id_value' => $id
            ]);
            throw $e;
        }
    }

    public function deleteExercise(string $id): bool
    {
        // Make sure we're working with just the ID string
        if ($id instanceof Exercise) {
            $id = $id->id;
        } elseif (is_array($id) && isset($id['id'])) {
            $id = $id['id'];
        } elseif (is_object($id) && isset($id->id)) {
            $id = $id->id;
        }

        $exercise = $this->exerciseRepository->find($id);

        if ($exercise->image_url) {
            Storage::delete(str_replace('/storage/', '', $exercise->image_url));
        }

        return $this->exerciseRepository->delete($id);
    }

    public function toggleStatus(string $id): Exercise
    {
        // Make sure we're working with just the ID string
        if ($id instanceof Exercise) {
            $id = $id->id;
        } elseif (is_array($id) && isset($id['id'])) {
            $id = $id['id'];
        } elseif (is_object($id) && isset($id->id)) {
            $id = $id->id;
        }

        $exercise = $this->exerciseRepository->find($id);
        return $this->exerciseRepository->update($id, [
            'is_active' => !$exercise->is_active
        ]);
    }

    public function searchExercises(string $query): LengthAwarePaginator
    {
        return $this->exerciseRepository->search($query);
    }

    public function getByCategory(string $categoryId): LengthAwarePaginator
    {
        return $this->exerciseRepository->getByCategory($categoryId);
    }

    /**
     * Helper method to ensure we always get a string ID
     */
    private function ensureStringId($id): string
    {
        if ($id instanceof Exercise) {
            return $id->id;
        } elseif (is_array($id) && isset($id['id'])) {
            return $id['id'];
        } elseif (is_object($id) && isset($id->id)) {
            return $id->id;
        }
        return (string) $id;
    }
}
