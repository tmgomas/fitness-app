<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Nutrition\Interfaces\DailyNutritionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NutritionSummaryController extends Controller
{
    protected $nutritionService;

    public function __construct(DailyNutritionServiceInterface $nutritionService)
    {
        $this->nutritionService = $nutritionService;
    }

    /**
     * Get full nutrition summary for the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSummary(Request $request): JsonResponse
    {
        try {
            $date = $request->get('date');
            $user = Auth::user();

            $summary = $this->nutritionService->getNutritionSummary($user, $date);

            return response()->json([
                'status' => 'success',
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve nutrition summary: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recommended calories based on health data and preferences
     *
     * @return JsonResponse
     */
    public function getRecommendedCalories(): JsonResponse
    {
        try {
            $user = Auth::user();
            $recommended = $this->nutritionService->getRecommendedCalories($user);

            return response()->json([
                'status' => 'success',
                'data' => $recommended,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve recommended calories: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get consumed calories for a specific date
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getConsumedCalories(Request $request): JsonResponse
    {
        try {
            $date = $request->get('date');
            $user = Auth::user();

            $consumed = $this->nutritionService->getConsumedCalories($user, $date);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'calories_consumed' => $consumed
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve consumed calories: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get burned calories for a specific date
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBurnedCalories(Request $request): JsonResponse
    {
        try {
            $date = $request->get('date');
            $user = Auth::user();

            $burned = $this->nutritionService->getBurnedCalories($user, $date);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'calories_burned' => $burned
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve burned calories: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get weekly summary of nutrition data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getWeeklySummary(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $endDate = $request->get('end_date') ? now()->parse($request->get('end_date')) : now();
            $startDate = $request->get('start_date') ? now()->parse($request->get('start_date')) : $endDate->copy()->subDays(6);

            $weeklySummary = [];
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                $dailySummary = $this->nutritionService->getNutritionSummary(
                    $user,
                    $currentDate->format('Y-m-d')
                );

                $weeklySummary[] = $dailySummary;
                $currentDate->addDay();
            }

            return response()->json([
                'status' => 'success',
                'data' => $weeklySummary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve weekly summary: ' . $e->getMessage(),
            ], 500);
        }
    }
}
