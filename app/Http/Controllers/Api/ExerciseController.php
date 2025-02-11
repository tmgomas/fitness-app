<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exercise\StoreExerciseRequest;
use App\Http\Requests\Exercise\UpdateExerciseRequest;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExerciseController extends Controller
{
    public function index(): JsonResponse
    {
        $exercises = Exercise::with(['category'])
            ->latest()
            ->paginate(10);

        return response()->json($exercises);
    }

    public function store(StoreExerciseRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('exercises', 'public');
            }

            // Create exercise
            $exercise = Exercise::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imagePath ? Storage::url($imagePath) : null,
                'difficulty_level' => $request->difficulty_level,
                'calories_per_minute' => $request->calories_per_minute,
                'calories_per_km' => $request->calories_per_km,
                'requires_distance' => $request->requires_distance,
                'requires_heartrate' => $request->requires_heartrate,
                'recommended_intensity' => $request->recommended_intensity,
                'is_active' => true
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Exercise created successfully',
                'exercise' => $exercise->load('category')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($imagePath)) {
                Storage::delete($imagePath);
            }
            return response()->json([
                'message' => 'Error creating exercise',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Exercise $exercise): JsonResponse
    {
        return response()->json($exercise->load('category'));
    }

    public function update(UpdateExerciseRequest $request, Exercise $exercise): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($exercise->image_url) {
                    Storage::delete(str_replace('/storage/', '', $exercise->image_url));
                }
                $imagePath = $request->file('image')->store('exercises', 'public');
                $exercise->image_url = Storage::url($imagePath);
            }

            // Update exercise
            $exercise->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'difficulty_level' => $request->difficulty_level,
                'calories_per_minute' => $request->calories_per_minute,
                'calories_per_km' => $request->calories_per_km,
                'requires_distance' => $request->requires_distance,
                'requires_heartrate' => $request->requires_heartrate,
                'recommended_intensity' => $request->recommended_intensity,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Exercise updated successfully',
                'exercise' => $exercise->load('category')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating exercise',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Exercise $exercise): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Delete image if exists
            if ($exercise->image_url) {
                Storage::delete(str_replace('/storage/', '', $exercise->image_url));
            }

            $exercise->delete();

            DB::commit();

            return response()->json(['message' => 'Exercise deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting exercise',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $exercises = Exercise::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('category')
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $exercises->items(),
            'current_page' => $exercises->currentPage(),
            'last_page' => $exercises->lastPage(),
            'per_page' => $exercises->perPage(),
            'total' => $exercises->total()
        ]);
    }

    public function getByCategory(Request $request, $categoryId): JsonResponse
    {
        $exercises = Exercise::where('category_id', $categoryId)
            ->where('is_active', true)
            ->with('category')
            ->latest()
            ->paginate(10);

        return response()->json($exercises);
    }

    public function toggleStatus(Exercise $exercise): JsonResponse
    {
        try {
            $exercise->update(['is_active' => !$exercise->is_active]);

            return response()->json([
                'message' => 'Exercise status updated successfully',
                'is_active' => $exercise->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating exercise status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
