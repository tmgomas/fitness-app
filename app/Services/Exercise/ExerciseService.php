<?php

namespace App\Services\Exercise;

use App\Models\Exercise;
use App\Models\UserCustomExercise;
use App\Repositories\Exercise\Interfaces\ExerciseRepositoryInterface;
use App\Services\Exercise\Interfaces\ExerciseServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Search exercises including user's custom exercises
     * 
     * @param string $query The search term
     * @return LengthAwarePaginator Combined paginated results
     */
    public function searchExercises(string $query): LengthAwarePaginator
    {
        // Get current authenticated user ID
        $userId = Auth::id();

        // Log the search request
        Log::info('Searching exercises', [
            'query' => $query,
            'user_id' => $userId
        ]);

        try {
            // First, get standard exercises
            $standardExercises = $this->exerciseRepository->search($query);
            $standardItems = $standardExercises->items();

            // Get the user's custom exercises matching the query
            $customExercises = UserCustomExercise::where('user_id', $userId)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->where('is_active', true)
                ->get();

            Log::info('Search results', [
                'standard_count' => count($standardItems),
                'custom_count' => $customExercises->count()
            ]);

            // No custom exercises found, just return standard results
            if ($customExercises->isEmpty()) {
                return $standardExercises;
            }

            // Prepare custom exercises for merging (with type indicator)
            $formattedCustomExercises = $customExercises->map(function ($exercise) {
                // Add an indicator that this is a custom exercise
                $exercise->is_custom = true;
                return $exercise;
            });

            // Prepare standard exercises for merging (with type indicator)
            $formattedStandardExercises = collect($standardItems)->map(function ($exercise) {
                // Add an indicator that this is a standard exercise
                $exercise->is_custom = false;
                return $exercise;
            });

            // Merge both collections
            $allExercises = $formattedStandardExercises->concat($formattedCustomExercises);

            // Sort combined results
            $sortedExercises = $allExercises->sortByDesc('created_at');

            // Create a new paginator with the combined results
            $page = Paginator::resolveCurrentPage() ?: 1;
            $perPage = $standardExercises->perPage();
            $items = $sortedExercises->forPage($page, $perPage);

            $paginator = new LengthAwarePaginator(
                $items,
                $sortedExercises->count(),
                $perPage,
                $page,
                ['path' => Paginator::resolveCurrentPath()]
            );

            return $paginator;
        } catch (\Exception $e) {
            Log::error('Error searching exercises: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // If an error occurs, return the standard result as fallback
            return $this->exerciseRepository->search($query);
        }
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
