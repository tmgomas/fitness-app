<?php
// app/Providers/RepositoryServiceProvider.php
// app/Providers/RepositoryServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\HealthData\HealthDataRepository;
use App\Repositories\HealthData\Interfaces\HealthDataRepositoryInterface;
use App\Repositories\UserPreference\Interfaces\UserPreferenceRepositoryInterface;
use App\Repositories\UserPreference\UserPreferenceRepository;
use App\Services\HealthData\HealthDataService;
use App\Services\HealthData\Interfaces\HealthDataServiceInterface;
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

        // Service Bindings
        $this->app->bind(
            HealthDataServiceInterface::class,
            HealthDataService::class
        );

        $this->app->bind(
            UserPreferenceServiceInterface::class,
            UserPreferenceService::class
        );
    }
}
