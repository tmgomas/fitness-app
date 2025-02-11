<?php
// app/Repositories/UserPreference/Interfaces/UserPreferenceRepositoryInterface.php
namespace App\Repositories\UserPreference\Interfaces;

interface UserPreferenceRepositoryInterface
{
    public function getAllForUser($userId);
    public function createForUser($userId, array $data);
    public function findForUser($userId, $prefId);
    public function updateForUser($userId, $prefId, array $data);
    public function deleteForUser($userId, $prefId);
}

