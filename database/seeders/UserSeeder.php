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

        // Create sample customer
        User::create([
            'name' => 'John Customer',
            'email' => 'customer@harvviie.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CUSTOMER,
            'phone' => '+27 82 123 4567',
            'address' => [
                'street' => '123 Main Street',
                'city' => 'Cape Town',
                'postal_code' => '8000',
                'country' => 'South Africa',
            ],
        ]);

        // Create additional test users (mix of roles)
        User::factory(5)->customer()->create();
        User::factory(2)->editor()->create();
        User::factory(1)->admin()->create();
    }
}