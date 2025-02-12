<?php
// app/Providers/RepositoryServiceProvider.php
// app/Providers/RepositoryServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\HealthData\HealthDataRepository;
use App\Repositories\HealthData\Interfaces\HealthDataRepositoryInterface;
use App\Repositories\Measurement\Interfaces\MeasurementRepositoryInterface;
use App\Repositories\Measurement\MeasurementRepository;
use App\Repositories\NutritionType\Interfaces\NutritionTypeRepositoryInterface;
use App\Repositories\NutritionType\NutritionTypeRepository;
use App\Repositories\UserPreference\Interfaces\UserPreferenceRepositoryInterface;
use App\Repositories\UserPreference\UserPreferenceRepository;
use App\Services\HealthData\HealthDataService;
use App\Services\HealthData\Interfaces\HealthDataServiceInterface;
use App\Services\Measurement\Interfaces\MeasurementServiceInterface;
use App\Services\Measurement\MeasurementService;
use App\Services\NutritionType\Interfaces\NutritionTypeServiceInterface;
use App\Services\NutritionType\NutritionTypeService;
use App\Services\UserPreference\Interfaces\UserPreferenceServiceInterface;
use App\Services\UserPreference\UserPreferenceService;

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
    }
}
