<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class UserController extends Controller
{
    protected $userService;
    use AuthorizesRequests;

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

            return view('users.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return back()->with('error', 'Error retrieving users.');
        }
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        Log::info('Starting user creation process', [
            'request_data' => $request->validated(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            DB::beginTransaction();

            $user = $this->userService->createUser($request->validated());

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]);

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating user', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'request_data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error creating user.');
        }
    }
    public function show(User $user)
    {
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
       
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            
            DB::beginTransaction();
            $user = $this->userService->updateUser($user, $request->validated());
            DB::commit();
            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->with('error', 'Error updating user.');
        }
    }

    public function destroy(User $user)
    {
        try {
           
            DB::beginTransaction();
            if (!$this->userService->canDeleteUser($user)) {
                throw new \Exception('This user cannot be deleted.');
            }
            $this->userService->deleteUser($user);
            DB::commit();
            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();
            $user = User::withTrashed()->findOrFail($id);
            $this->authorize('restore', $user);
            $this->userService->restoreUser($user);
            DB::commit();
            return redirect()->route('users.index')->with('success', 'User restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring user: ' . $e->getMessage());
            return back()->with('error', 'Error restoring user.');
        }
    }

    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();
            $user = User::withTrashed()->findOrFail($id);
            $this->authorize('forceDelete', $user);
            if (!$this->userService->canForceDeleteUser($user)) {
                throw new \Exception('User cannot be permanently deleted.');
            }
            $this->userService->forceDeleteUser($user);
            DB::commit();
            return redirect()->route('users.index')->with('success', 'User permanently deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error force deleting user: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }
}
