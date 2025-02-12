<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodItem\StoreFoodItemRequest;
use App\Http\Requests\FoodItem\UpdateFoodItemRequest;
use App\Services\FoodItem\Interfaces\FoodItemServiceInterface;
use App\Services\NutritionType\Interfaces\NutritionTypeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FoodItemController extends Controller
{
    public function __construct(
        private readonly FoodItemServiceInterface $foodItemService,
        private readonly NutritionTypeServiceInterface $nutritionTypeService
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status')
        ];

        $foodItems = $this->foodItemService->getAllFoodItems($filters);

        return view('food-items.index', compact('foodItems'));
    }

    public function create(): View
    {
        $nutritionTypes = $this->nutritionTypeService->getAllNutritionTypes();
        return view('food-items.create', compact('nutritionTypes'));
    }

    public function store(StoreFoodItemRequest $request): RedirectResponse
    {
        try {
            $this->foodItemService->createFoodItem($request->validated());

            return redirect()
                ->route('food-items.index')
                ->with('success', 'Food item created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error creating food item: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(string $id): View
    {
        $foodItem = $this->foodItemService->getFoodItemById($id);
        $nutritionTypes = $this->nutritionTypeService->getAllNutritionTypes();
        $existingNutritionData = $foodItem->foodNutrition->keyBy('nutrition_id');

        return view('food-items.edit', compact('foodItem', 'nutritionTypes', 'existingNutritionData'));
    }

    public function update(UpdateFoodItemRequest $request, string $id): RedirectResponse
    {
        try {
            $this->foodItemService->updateFoodItem($id, $request->validated());

            return redirect()
                ->route('food-items.index')
                ->with('success', 'Food item updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error updating food item: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->foodItemService->deleteFoodItem($id);

            return redirect()
                ->route('food-items.index')
                ->with('success', 'Food item deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error deleting food item: ' . $e->getMessage());
        }
    }

    public function show(string $id): View
    {
        $foodItem = $this->foodItemService->getFoodItemById($id);
        return view('food-items.show', compact('foodItem'));
    }
}
