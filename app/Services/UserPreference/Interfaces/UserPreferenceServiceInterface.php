<?php
// app/Services/UserPreference/Interfaces/UserPreferenceServiceInterface.php
namespace App\Services\UserPreference\Interfaces;

interface UserPreferenceServiceInterface
{
    public function getUserPreferences();
    public function createPreference(array $data);
    public function getPreference($id);
    public function updatePreference($id, array $data);
    public function deletePreference($id);
}
