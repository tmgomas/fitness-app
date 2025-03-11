<?php

namespace App\Services\UserProfile\Interfaces;

interface UserProfileServiceInterface
{
    public function getProfile();
    public function updateProfile(array $data);
    public function updatePassword(string $currentPassword, string $newPassword);
    public function uploadProfilePicture($imageFile);
    public function deleteProfilePicture();
}
