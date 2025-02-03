<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NutritionType;
use App\Http\Requests\NutritionType\StoreNutritionTypeRequest;
use App\Http\Requests\NutritionType\UpdateNutritionTypeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NutritionTypeController extends Controller
{
    public function index(): View
    {
        $nutritionTypes = NutritionType::latest()->paginate(10);
        return view('nutrition-types.index', compact('nutritionTypes'));
    }

    public function create(): View
    {
        return view('nutrition-types.create');
    }

    public function store(StoreNutritionTypeRequest $request): RedirectResponse
    {
        NutritionType::create($request->validated());
        
        return redirect()
            ->route('nutrition-types.index')
            ->with('success', 'Nutrition type created successfully.');
    }

    public function edit(NutritionType $nutritionType): View
    {
        return view('nutrition-types.edit', compact('nutritionType'));
    }

    public function update(UpdateNutritionTypeRequest $request, NutritionType $nutritionType): RedirectResponse
    {
        $nutritionType->update($request->validated());
        
        return redirect()
            ->route('nutrition-types.index')
            ->with('success', 'Nutrition type updated successfully.');
    }

    public function destroy(NutritionType $nutritionType): RedirectResponse
    {
        $nutritionType->delete();
        
        return redirect()
            ->route('nutrition-types.index')
            ->with('success', 'Nutrition type deleted successfully.');
    }
}