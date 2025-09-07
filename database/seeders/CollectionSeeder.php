<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        // Create collections
        $collections = Collection::factory(8)->create();
        
        // Get all products
        $products = Product::all();

        // Assign random products to collections
        foreach ($collections as $collection) {
            $randomProducts = $products->random(fake()->numberBetween(3, 8));
            
            $syncData = [];
            foreach ($randomProducts as $index => $product) {
                $syncData[$product->id] = ['position' => $index + 1];
            }
            
            $collection->products()->sync($syncData);
        }

        // Create a featured "Latest Collection"
        Collection::create([
            'name' => 'Latest Collection',
            'slug' => 'latest-collection',
            'description' => 'Our newest arrivals featuring the latest trends in fashion.',
            'cover_image' => 'https://images.pexels.com/photos/996329/pexels-photo-996329.jpeg',
            'is_active' => true,
        ]);
    }
}