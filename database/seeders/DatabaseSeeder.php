<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
            CollectionSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
            BannerSeeder::class,
            AboutSeeder::class,
            MessageSeeder::class,
        ]);
    }
}