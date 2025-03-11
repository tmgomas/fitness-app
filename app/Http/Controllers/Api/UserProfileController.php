<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserProfile\UpdatePasswordRequest;
use App\Http\Requests\UserProfile\UpdateProfileRequest;
use App\Http\Requests\UserProfile\UploadProfilePictureRequest;
use App\Http\Resources\UserProfileResource;
use App\Services\UserProfile\Interfaces\UserProfileServiceInterface;
use Illuminate\Http\JsonResponse;

class UserProfileController extends Controller
{
    protected $userProfileService;

    public function __construct(UserProfileServiceInterface $userProfileService)
    {
        $this->userProfileService = $userProfileService;
    }

    /**
     * Get the authenticated user's profile
     */
    public function getProfile(): JsonResponse
    {
        try {
            $user = $this->userProfileService->getProfile();
            return response()->json([
                'status' => 'success',
                'data' => new UserProfileResource($user)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the authenticated user's profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = $this->userProfileService->updateProfile($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => new UserProfileResource($user)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the authenticated user's password
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $this->userProfileService->updatePassword(
                $request->current_password,
                $request->password
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture(UploadProfilePictureRequest $request): JsonResponse
    {
        try {
            $user = $this->userProfileService->uploadProfilePicture($request->file('profile_picture'));

            return response()->json([
                'status' => 'success',
                'message' => 'Profile picture uploaded successfully',
                'data' => [
                    'profile_picture' => asset('storage/' . $user->profile_picture)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error uploading profile picture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete profile picture
     */
    public function deleteProfilePicture(): JsonResponse
    {
        try {
            $this->userProfileService->deleteProfilePicture();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile picture deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting profile picture: ' . $e->getMessage()
            ], 500);
        }
    }
}
