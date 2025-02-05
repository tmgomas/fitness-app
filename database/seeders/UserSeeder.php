<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'dgtmgomas@hotmail.com',
            'password' => Hash::make('10334548'),
            'is_active' => true,
            'is_admin' => true,
            'gender' => 'male',
            'birthday' => '1990-01-01',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Regular User',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_admin' => false,
            'gender' => 'female',
            'birthday' => '1995-05-05',
            'email_verified_at' => now(),
        ]);

        // Generate 10 random users
        User::factory(10)->create();
    }
}
