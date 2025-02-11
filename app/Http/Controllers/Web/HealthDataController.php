<?php
// app/Http/Controllers/Web/HealthDataController.php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\HealthData\StoreHealthDataRequest;
use App\Http\Requests\HealthData\UpdateHealthDataRequest;
use App\Services\HealthData\Interfaces\HealthDataServiceInterface;

class HealthDataController extends Controller
{
    protected $healthDataService;

    public function __construct(HealthDataServiceInterface $healthDataService)
    {
        $this->healthDataService = $healthDataService;
    }

    public function index()
    {
        $healthData = $this->healthDataService->getAllHealthData();
        return view('health-data.index', compact('healthData'));
    }

    public function create()
    {
        return view('health-data.create');
    }

    public function store(StoreHealthDataRequest $request)
    {
        try {
            $this->healthDataService->storeHealthData($request->validated());
            return redirect()->route('health-data.index')->with('success', 'Health data saved successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error saving health data')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $healthData = $this->healthDataService->getHealthData($id);
            return view('health-data.edit', compact('healthData'));
        } catch (\Exception $e) {
            return redirect()->route('health-data.index')->with('error', 'Health data not found');
        }
    }

    public function update(UpdateHealthDataRequest $request, $id)
    {
        try {
            $this->healthDataService->updateHealthData($id, $request->validated());
            return redirect()->route('health-data.index')->with('success', 'Health data updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating health data')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->healthDataService->deleteHealthData($id);
            return redirect()->route('health-data.index')->with('success', 'Health data deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting health data');
        }
    }
}
