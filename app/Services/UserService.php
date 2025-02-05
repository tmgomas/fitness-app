<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function createUser(array $data)
    {
        $data['username'] = $this->generateUniqueUsername($data['name']);
        $data['password'] = Hash::make($this->generateRandomPassword());
        return User::create($data);
    }

    private function generateUniqueUsername($name)
    {
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    private function generateRandomPassword()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    public function getPaginatedUsers($search = null, $role = null, $status = null, $trashed = null, $perPage = 10)
    {
        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($role === 'admin') {
            $query->where('is_admin', true);
        } elseif ($role === 'user') {
            $query->where('is_admin', false);
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($trashed === 'with') {
            $query->withTrashed();
        } elseif ($trashed === 'only') {
            $query->onlyTrashed();
        }

        return $query->latest()->paginate($perPage);
    }



    public function updateUser(User $user, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user)
    {
        return $user->delete();
    }

    public function restoreUser(User $user)
    {
        return $user->restore();
    }

    public function forceDeleteUser(User $user)
    {
        return $user->forceDelete();
    }

    public function canDeleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return false;
        }
        return true;
    }

    public function canForceDeleteUser(User $user)
    {
        if ($user->id === Auth::id() || $user->isAdmin()) {
            return false;
        }
        return true;
    }

    public function login(string $username, string $password)
    {
        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Your account is inactive. Please contact administrator.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ];
    }
}
