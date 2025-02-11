<?php

// app/Services/UserPreference/UserPreferenceService.php
namespace App\Services\UserPreference;

use App\Services\UserPreference\Interfaces\UserPreferenceServiceInterface;
use App\Repositories\UserPreference\Interfaces\UserPreferenceRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserPreferenceService implements UserPreferenceServiceInterface
{
    protected $repository;

    public function __construct(UserPreferenceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getUserPreferences()
    {
        return $this->repository->getAllForUser(Auth::id());

        try {
            return $this->repository->getAllForUser(Auth::id());
        } catch (\Exception $e) {
            Log::error('Error fetching health data: ' . $e->getMessage());
            throw $e;
        }
    }


    public function createPreference(array $data)
    {
        return $this->repository->createForUser(Auth::id(), $data);
    }

    public function getPreference($id)
    {
        return $this->repository->findForUser(Auth::id(), $id);
    }

    public function updatePreference($id, array $data)
    {
        return $this->repository->updateForUser(Auth::id(), $id, $data);
    }

    public function deletePreference($id)
    {
        return $this->repository->deleteForUser(Auth::id(), $id);
    }
}
