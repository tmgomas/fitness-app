<?php

namespace App\Services\UserProfile;

use App\Services\UserProfile\Interfaces\UserProfileServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserProfileService implements UserProfileServiceInterface
{
    public function getProfile()
    {
        try {
            return Auth::user();
        } catch (\Exception $e) {
            Log::error('Error fetching user profile: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateProfile(array $data)
    {
        try {
            $user = Auth::user();

            // Check if email is being updated
            if (isset($data['email']) && $data['email'] !== $user->email) {
                $data['email_verified_at'] = null;
                // You could add code to send a new verification email here
            }

            $user->update($data);
            return $user->refresh();
        } catch (\Exception $e) {
            Log::error('Error updating user profile: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updatePassword(string $currentPassword, string $newPassword)
    {
        try {
            $user = Auth::user();

            // Verify current password
            if (!Hash::check($currentPassword, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['The provided password does not match our records.'],
                ]);
            }

            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            throw $e;
        }
    }

    public function uploadProfilePicture($imageFile)
    {
        try {
            $user = Auth::user();

            // Delete old profile picture if it exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store new profile picture
            $path = $imageFile->store('profile-pictures', 'public');

            $user->update([
                'profile_picture' => $path
            ]);

            return $user->refresh();
        } catch (\Exception $e) {
            Log::error('Error uploading profile picture: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProfilePicture()
    {
        try {
            $user = Auth::user();

            // Delete profile picture if it exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $user->update([
                'profile_picture' => null
            ]);

            return $user->refresh();
        } catch (\Exception $e) {
            Log::error('Error deleting profile picture: ' . $e->getMessage());
            throw $e;
        }
    }
}
