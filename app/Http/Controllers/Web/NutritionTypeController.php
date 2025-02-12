<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\NutritionType\Interfaces\NutritionTypeServiceInterface;
use App\Http\Requests\NutritionType\StoreNutritionTypeRequest;
use App\Http\Requests\NutritionType\UpdateNutritionTypeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class NutritionTypeController extends Controller
{
    protected $nutritionTypeService;

    public function __construct(NutritionTypeServiceInterface $nutritionTypeService)
    {
        $this->nutritionTypeService = $nutritionTypeService;
    }

    public function index(): View
    {
        try {
            $nutritionTypes = $this->nutritionTypeService->getAllNutritionTypes();
            return view('nutrition-types.index', compact('nutritionTypes'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create(): View
    {
        try {
            return view('nutrition-types.create');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(StoreNutritionTypeRequest $request): RedirectResponse
    {
        try {
            $this->nutritionTypeService->createNutritionType($request->validated());
            return redirect()
                ->route('nutrition-types.index')
                ->with('success', 'Nutrition type created successfully.');
        } catch (Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit(string $nutritionId): View
    {
        try {
            $nutritionType = $this->nutritionTypeService->getNutritionType($nutritionId);
            return view('nutrition-types.edit', compact('nutritionType'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(UpdateNutritionTypeRequest $request, string $nutritionId): RedirectResponse
    {
        try {
            $this->nutritionTypeService->updateNutritionType($nutritionId, $request->validated());
            return redirect()
                ->route('nutrition-types.index')
                ->with('success', 'Nutrition type updated successfully.');
        } catch (Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $nutritionId): RedirectResponse
    {
        try {
            $this->nutritionTypeService->deleteNutritionType($nutritionId);
            return redirect()
                ->route('nutrition-types.index')
                ->with('success', 'Nutrition type deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
