<?php

// app/Providers/RepositoryServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserPreference\Interfaces\UserPreferenceRepositoryInterface;
use App\Repositories\UserPreference\UserPreferenceRepository;
use App\Services\UserPreference\Interfaces\UserPreferenceServiceInterface;
use App\Services\UserPreference\UserPreferenceService;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        
    }
}
