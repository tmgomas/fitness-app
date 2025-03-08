<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Report\Interfaces\MonthlyReportServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MonthlyReportController extends Controller
{
    protected $monthlyReportService;

    public function __construct(MonthlyReportServiceInterface $monthlyReportService)
    {
        $this->monthlyReportService = $monthlyReportService;
    }

    /**
     * Get monthly summary for calories consumed and burned
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getMonthlyCaloriesSummary(Request $request): JsonResponse
    {
        try {
            // Get requested month or default to current month
            $year = $request->input('year', Carbon::now()->year);
            $month = $request->input('month', Carbon::now()->month);

            // Validate month and year
            if ($month < 1 || $month > 12 || $year < 2000 || $year > Carbon::now()->year + 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid month or year provided'
                ], 400);
            }

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();

            // Get summary data
            $summary = $this->monthlyReportService->getMonthlyCaloriesSummary(Auth::id(), $startDate, $endDate);

            return response()->json([
                'status' => 'success',
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching monthly calories summary: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the monthly summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed daily data for the specified month
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getMonthlyCaloriesDetails(Request $request): JsonResponse
    {
        try {
            // Get requested month or default to current month
            $year = $request->input('year', Carbon::now()->year);
            $month = $request->input('month', Carbon::now()->month);

            // Validate month and year
            if ($month < 1 || $month > 12 || $year < 2000 || $year > Carbon::now()->year + 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid month or year provided'
                ], 400);
            }

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();

            // Get detailed data
            $details = $this->monthlyReportService->getMonthlyCaloriesDetails(Auth::id(), $startDate, $endDate);

            return response()->json([
                'status' => 'success',
                'data' => $details
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching monthly calories details: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the monthly details: ' . $e->getMessage()
            ], 500);
        }
    }
}
