<?php
// app/Http/Controllers/MealController.php
namespace App\Http\Controllers\Web;

use App\Http\Requests\Meal\StoreMealRequest;
use App\Http\Requests\Meal\UpdateMealRequest;
use App\Models\FoodItem;
use App\Models\NutritionType;
use App\Services\Meal\Interfaces\MealServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class MealController extends Controller
{
    protected $mealService;

    public function __construct(MealServiceInterface $mealService)
    {
        $this->mealService = $mealService;
    }

    public function index(Request $request)
    {
        $meals = $this->mealService->getAllMeals();
        return view('meals.index', compact('meals'));
    }

    public function create()
    {
        $foodItems = FoodItem::where('is_active', true)->orderBy('name')->get();
        $nutritionTypes = NutritionType::where('is_active', true)->orderBy('name')->get();
        return view('meals.create', compact('foodItems', 'nutritionTypes'));
    }

    public function store(StoreMealRequest $request)
    {
        try {
            $meal = $this->mealService->createMeal($request->validated());
            return redirect()->route('meals.index')
                ->with('success', 'Meal created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating meal: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error creating meal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $meal = $this->mealService->getMeal($id);
        return view('meals.show', compact('meal'));
    }

    public function edit($id)
    {
        $meal = $this->mealService->getMeal($id);
        $foodItems = FoodItem::where('is_active', true)->orderBy('name')->get();
        $nutritionTypes = NutritionType::where('is_active', true)->orderBy('name')->get();
        return view('meals.edit', compact('meal', 'foodItems', 'nutritionTypes'));
    }

    public function update(UpdateMealRequest $request, $id)
    {
        try {
            $this->mealService->updateMeal($id, $request->validated());
            return redirect()->route('meals.index')
                ->with('success', 'Meal updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating meal: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error updating meal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->mealService->deleteMeal($id);
            return redirect()->route('meals.index')
                ->with('success', 'Meal deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting meal: ' . $e->getMessage());
            return back()
                ->with('error', 'Error deleting meal: ' . $e->getMessage());
        }
    }
}
