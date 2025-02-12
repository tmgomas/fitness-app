<?php
// app/Http/Controllers/ExerciseCategoryController.php
namespace App\Http\Controllers;

use App\Http\Requests\ExerciseCategory\StoreExerciseCategoryRequest;
use App\Http\Requests\ExerciseCategory\UpdateExerciseCategoryRequest;
use App\Services\ExerciseCategory\Interfaces\ExerciseCategoryServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExerciseCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(ExerciseCategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): View
    {
        $categories = $this->categoryService->getAllCategories();
        return view('exercise-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('exercise-categories.create');
    }

    public function store(StoreExerciseCategoryRequest $request): RedirectResponse
    {
        try {
            $this->categoryService->createCategory($request->validated());
            return redirect()
                ->route('exercise-categories.index')
                ->with('success', 'Exercise category created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error creating exercise category: ' . $e->getMessage());
        }
    }

    public function edit($id): View
    {
        $exerciseCategory = $this->categoryService->getCategory($id);
        return view('exercise-categories.edit', compact('exerciseCategory'));
    }

    public function update(UpdateExerciseCategoryRequest $request, $id): RedirectResponse
    {
        try {
            $this->categoryService->updateCategory($id, $request->validated());
            return redirect()
                ->route('exercise-categories.index')
                ->with('success', 'Exercise category updated successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating exercise category: ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $this->categoryService->deleteCategory($id);
            return redirect()
                ->route('exercise-categories.index')
                ->with('success', 'Exercise category deleted successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error deleting exercise category: ' . $e->getMessage());
        }
    }
}
