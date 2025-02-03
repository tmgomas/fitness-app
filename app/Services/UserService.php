<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getPaginatedUsers($search = null, $role = null, $status = null, $perPage = 10)
    {
        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->where('is_admin', $role === 'admin');
        }

        if ($status) {
            $query->where('is_active', $status === 'active');
        }

        return $query->latest()->paginate($perPage);
    }

    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function updateUser(User $user, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user)
    {
        return $user->delete();
    }

    public function canDeleteUser(User $user)
    {
        // Add any logic to check if user can be deleted
        return true;
    }
}