<?php
// app/Http/Controllers/Api/ExerciseCategoryController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseCategory\StoreExerciseCategoryRequest;
use App\Http\Requests\ExerciseCategory\UpdateExerciseCategoryRequest;
use App\Http\Resources\ExerciseCategoryResource;
use App\Services\ExerciseCategory\Interfaces\ExerciseCategoryServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExerciseCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(ExerciseCategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json(ExerciseCategoryResource::collection($categories));
    }

    public function store(StoreExerciseCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->categoryService->createCategory($request->validated());
            return response()->json([
                'message' => 'Exercise category created successfully',
                'data' => new ExerciseCategoryResource($category)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating exercise category',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        $category = $this->categoryService->getCategory($id);
        return response()->json(new ExerciseCategoryResource($category));
    }

    public function update(UpdateExerciseCategoryRequest $request, $id): JsonResponse
    {
        try {
            $category = $this->categoryService->updateCategory($id, $request->validated());
            return response()->json([
                'message' => 'Exercise category updated successfully',
                'data' => new ExerciseCategoryResource($category)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating exercise category',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->categoryService->deleteCategory($id);
            return response()->json([
                'message' => 'Exercise category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting exercise category',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
