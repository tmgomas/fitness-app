<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'dgtmgomas@hotmail.com',
            'password' => Hash::make('10334548'),
            'is_active' => true,
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Create Regular User
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

       #
    }
}