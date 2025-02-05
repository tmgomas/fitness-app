<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        try {
            $users = $this->userService->getPaginatedUsers(
                $request->get('search'),
                $request->get('role'),
                $request->get('status'),
                $request->get('trashed'),
                $request->get('per_page', 10)
            );
            return response()->json(['data' => $users]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching users'
            ], 500);
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->createUser($request->validated());
            return response()->json([
                'status' => 'success',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating user'
            ], 500);
        }
    }

    public function show(User $user)
    {
        return response()->json(['data' => $user]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $user = $this->userService->updateUser($user, $request->validated());
            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating user'
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            if (!$this->userService->canDeleteUser($user)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User cannot be deleted'
                ], 403);
            }

            $this->userService->deleteUser($user);
            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting user'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $result = $this->userService->login(
                $request->email,
                $request->password
            );

            return response()->json($result);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during login'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    public function restore($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $this->userService->restoreUser($user);
            return response()->json([
                'status' => 'success',
                'message' => 'User restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error restoring user'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            if (!$this->userService->canForceDeleteUser($user)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User cannot be permanently deleted'
                ], 403);
            }
            $this->userService->forceDeleteUser($user);
            return response()->json([
                'status' => 'success',
                'message' => 'User permanently deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error force deleting user'
            ], 500);
        }
    }
}
