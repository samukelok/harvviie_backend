<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@harvviie.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        // Create editor user
        User::create([
            'name' => 'Editor User',
            'email' => 'editor@harvviie.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_EDITOR,
        ]);

        // Create additional test users
        User::factory(8)->create();
    }
}