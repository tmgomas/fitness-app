<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Http\Requests\FoodItem\StoreFoodItemRequest;
use App\Http\Requests\FoodItem\UpdateFoodItemRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FoodItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = FoodItem::query();

        // Search
        if ($request->has('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $foodItems = $query->latest()->paginate(10);

        return view('food-items.index', compact('foodItems'));
    }

    public function create(): View
    {
        return view('food-items.create');
    }

    public function store(StoreFoodItemRequest $request): RedirectResponse
    {
        FoodItem::create($request->validated());
        
        return redirect()
            ->route('food-items.index')
            ->with('success', 'Food item created successfully.');
    }

    public function edit(FoodItem $foodItem): View
    {
        return view('food-items.edit', compact('foodItem'));
    }

    public function update(UpdateFoodItemRequest $request, FoodItem $foodItem): RedirectResponse
    {
        $foodItem->update($request->validated());
        
        return redirect()
            ->route('food-items.index')
            ->with('success', 'Food item updated successfully.');
    }

    public function destroy(FoodItem $foodItem): RedirectResponse
    {
        $foodItem->delete();
        
        return redirect()
            ->route('food-items.index')
            ->with('success', 'Food item deleted successfully.');
    }
}