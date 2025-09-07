<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create orders with various statuses
        Order::factory(30)->create();
        
        // Create some pending orders for dashboard
        Order::factory(5)->pending()->create();
        
        // Create some completed orders
        Order::factory(10)->completed()->create();
    }
}