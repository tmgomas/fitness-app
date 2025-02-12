<?php
// app/Http/Controllers/Api/MealController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meal\StoreMealRequest;
use App\Http\Requests\Meal\UpdateMealRequest;
use App\Http\Resources\MealResource;
use App\Services\Meal\Interfaces\MealServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MealController extends Controller
{
    protected $mealService;

    public function __construct(MealServiceInterface $mealService)
    {
        $this->mealService = $mealService;
    }

    public function index(): JsonResponse
    {
        $meals = $this->mealService->getAllMeals();
        return response()->json(MealResource::collection($meals));
    }

    public function store(StoreMealRequest $request): JsonResponse
    {
        try {
            $meal = $this->mealService->createMeal($request->validated());
            return response()->json([
                'message' => 'Meal created successfully',
                'data' => new MealResource($meal)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating meal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $meal = $this->mealService->getMeal($id);
        return response()->json(new MealResource($meal));
    }

    public function update(UpdateMealRequest $request, $id): JsonResponse
    {
        try {
            $meal = $this->mealService->updateMeal($id, $request->validated());
            return response()->json([
                'message' => 'Meal updated successfully',
                'data' => new MealResource($meal)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating meal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->mealService->deleteMeal($id);
            return response()->json([
                'message' => 'Meal deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting meal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // app/Http/Controllers/MealController.php
    // app/Http/Controllers/MealController.php
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $meals = $this->mealService->searchMeals($query);

            return response()->json([  // Note: Wrapped in an object
                'data' => [
                    'items' => $meals->isEmpty() ? [] : MealResource::collection($meals)->response()->getData()->data,
                    'total' => $meals->total(),
                    'currentPage' => $meals->currentPage(),
                    'perPage' => $meals->perPage(),
                    'lastPage' => $meals->lastPage()
                ],
                'message' => $meals->isEmpty() ? 'No meals found matching your search criteria.' : 'Meals retrieved successfully.',
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [
                    'items' => [],
                    'total' => 0,
                    'currentPage' => 1,
                    'perPage' => 10,
                    'lastPage' => 1
                ],
                'message' => 'An error occurred while searching meals.',
                'error' => $e->getMessage(),
                'success' => false
            ]);
        }
    }
}
