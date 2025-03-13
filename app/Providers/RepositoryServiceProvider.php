<?php
// app/Providers/RepositoryServiceProvider.php
// app/Providers/RepositoryServiceProvider.php
namespace App\Providers;

use App\Repositories\Exercise\ExerciseRepository;
use App\Repositories\Exercise\Interfaces\ExerciseRepositoryInterface;
use App\Repositories\ExerciseCategory\ExerciseCategoryRepository;
use App\Repositories\ExerciseCategory\Interfaces\ExerciseCategoryRepositoryInterface;
use App\Repositories\ExerciseIntensity\ExerciseIntensityRepository;
use App\Repositories\ExerciseIntensity\Interfaces\ExerciseIntensityRepositoryInterface;
use App\Repositories\FoodItem\FoodItemRepository;
use App\Repositories\FoodItem\Interfaces\FoodItemRepositoryInterface;
use App\Repositories\FoodNutrition\FoodNutritionRepository;
use App\Repositories\FoodNutrition\Interfaces\FoodNutritionRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\HealthData\HealthDataRepository;
use App\Repositories\HealthData\Interfaces\HealthDataRepositoryInterface;
use App\Repositories\Meal\Interfaces\MealRepositoryInterface;
use App\Repositories\Meal\MealRepository;
use App\Repositories\Measurement\Interfaces\MeasurementRepositoryInterface;
use App\Repositories\Measurement\MeasurementRepository;
use App\Repositories\NutritionType\Interfaces\NutritionTypeRepositoryInterface;
use App\Repositories\NutritionType\NutritionTypeRepository;
use App\Repositories\UserExerciseLog\Interfaces\UserExerciseLogRepositoryInterface;
use App\Repositories\UserExerciseLog\UserExerciseLogRepository;
use App\Repositories\UserFoodLog\Interfaces\UserFoodLogRepositoryInterface;
use App\Repositories\UserFoodLog\UserFoodLogRepository;
use App\Repositories\UserMealLog\Interfaces\UserMealLogRepositoryInterface;
use App\Repositories\UserMealLog\UserMealLogRepository;
use App\Repositories\UserPreference\Interfaces\UserPreferenceRepositoryInterface;
use App\Repositories\UserPreference\UserPreferenceRepository;
use App\Services\Exercise\ExerciseService;
use App\Services\Exercise\Interfaces\ExerciseServiceInterface;
use App\Services\ExerciseCategory\ExerciseCategoryService;
use App\Services\ExerciseCategory\Interfaces\ExerciseCategoryServiceInterface;
use App\Services\ExerciseIntensity\ExerciseIntensityService;
use App\Services\ExerciseIntensity\Interfaces\ExerciseIntensityServiceInterface;
use App\Services\FoodItem\FoodItemService;
use App\Services\FoodItem\Interfaces\FoodItemServiceInterface;
use App\Services\FoodNutrition\FoodNutritionService;
use App\Services\FoodNutrition\Interfaces\FoodNutritionServiceInterface;
use App\Services\HealthData\HealthDataService;
use App\Services\HealthData\Interfaces\HealthDataServiceInterface;
use App\Services\Meal\Interfaces\MealServiceInterface;
use App\Services\Meal\MealService;
use App\Services\Measurement\Interfaces\MeasurementServiceInterface;
use App\Services\Measurement\MeasurementService;
use App\Services\Nutrition\DailyNutritionService;
use App\Services\Nutrition\Interfaces\DailyNutritionServiceInterface;
use App\Services\NutritionType\Interfaces\NutritionTypeServiceInterface;
use App\Services\NutritionType\NutritionTypeService;
use App\Services\Report\Interfaces\MonthlyReportServiceInterface;
use App\Services\Report\MonthlyReportService;
use App\Services\UserExerciseLog\Interfaces\UserExerciseLogServiceInterface;
use App\Services\UserExerciseLog\UserExerciseLogService;
use App\Services\UserFoodLog\Interfaces\UserFoodLogServiceInterface;
use App\Services\UserFoodLog\UserFoodLogService;
use App\Services\UserMealLog\Interfaces\UserMealLogServiceInterface;
use App\Services\UserMealLog\UserMealLogService;
use App\Services\UserPreference\Interfaces\UserPreferenceServiceInterface;
use App\Services\UserPreference\UserPreferenceService;
use App\Services\UserProfile\Interfaces\UserProfileServiceInterface;
use App\Services\UserProfile\UserProfileService;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Repository Bindings
        $this->app->bind(
            HealthDataRepositoryInterface::class,
            HealthDataRepository::class
        );

        $this->app->bind(
            UserPreferenceRepositoryInterface::class,
            UserPreferenceRepository::class
        );

        $this->app->bind(
            MeasurementRepositoryInterface::class,
            MeasurementRepository::class
        );

        $this->app->bind(
            NutritionTypeRepositoryInterface::class,
            NutritionTypeRepository::class
        );
        $this->app->bind(
            FoodItemRepositoryInterface::class,
            FoodItemRepository::class
        );

        $this->app->bind(
            FoodNutritionRepositoryInterface::class,
            FoodNutritionRepository::class
        );
        $this->app->bind(
            MealRepositoryInterface::class,
            MealRepository::class
        );

        $this->app->bind(
            ExerciseCategoryRepositoryInterface::class,
            ExerciseCategoryRepository::class
        );
        $this->app->bind(
            ExerciseRepositoryInterface::class,
            ExerciseRepository::class
        );

        $this->app->bind(
            ExerciseIntensityRepositoryInterface::class,
            ExerciseIntensityRepository::class
        );

        $this->app->bind(
            UserMealLogRepositoryInterface::class,
            UserMealLogRepository::class
        );
        $this->app->bind(
            UserFoodLogRepositoryInterface::class,
            UserFoodLogRepository::class
        );
        $this->app->bind(
            UserExerciseLogRepositoryInterface::class,
            UserExerciseLogRepository::class
        );
        // Service Bindings
        $this->app->bind(
            HealthDataServiceInterface::class,
            HealthDataService::class
        );

        $this->app->bind(
            UserPreferenceServiceInterface::class,
            UserPreferenceService::class
        );

        $this->app->bind(
            MeasurementServiceInterface::class,
            MeasurementService::class
        );
        $this->app->bind(
            NutritionTypeServiceInterface::class,
            NutritionTypeService::class
        );
        $this->app->bind(
            FoodItemServiceInterface::class,
            FoodItemService::class
        );

        $this->app->bind(
            FoodNutritionServiceInterface::class,
            FoodNutritionService::class
        );
        $this->app->bind(
            MealServiceInterface::class,
            MealService::class
        );
        $this->app->bind(
            ExerciseCategoryServiceInterface::class,
            ExerciseCategoryService::class
        );

        $this->app->bind(
            ExerciseServiceInterface::class,
            ExerciseService::class
        );

        $this->app->bind(
            ExerciseIntensityServiceInterface::class,
            ExerciseIntensityService::class
        );
        $this->app->bind(
            ExerciseIntensityServiceInterface::class,
            ExerciseIntensityService::class
        );
        $this->app->bind(
            UserMealLogServiceInterface::class,
            UserMealLogService::class
        );

        $this->app->bind(
            UserFoodLogServiceInterface::class,
            UserFoodLogService::class
        );

        $this->app->bind(
            UserExerciseLogServiceInterface::class,
            UserExerciseLogService::class
        );

        $this->app->bind(
            DailyNutritionServiceInterface::class,
            DailyNutritionService::class

        );
        $this->app->bind(
            MonthlyReportServiceInterface::class,
            MonthlyReportService::class

        );
        $this->app->bind(
            UserProfileServiceInterface::class,
            UserProfileService::class
        );

        $this->app->bind(
            \App\Repositories\CustomExercise\Interfaces\CustomExerciseRepositoryInterface::class,
            \App\Repositories\CustomExercise\CustomExerciseRepository::class
        );

        $this->app->bind(
            \App\Services\CustomExercise\Interfaces\CustomExerciseServiceInterface::class,
            \App\Services\CustomExercise\CustomExerciseService::class
        );
    }
}
