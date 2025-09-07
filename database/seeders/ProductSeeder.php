<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create products with sample images
        $products = Product::factory(25)->create();

        // Sample Pexels fashion images
        $sampleImages = [
            'https://images.pexels.com/photos/996329/pexels-photo-996329.jpeg',
            'https://images.pexels.com/photos/1536619/pexels-photo-1536619.jpeg',
            'https://images.pexels.com/photos/1043474/pexels-photo-1043474.jpeg',
            'https://images.pexels.com/photos/1040173/pexels-photo-1040173.jpeg',
            'https://images.pexels.com/photos/1183266/pexels-photo-1183266.jpeg',
        ];

        foreach ($products as $product) {
            // Add 1-3 images per product
            $imageCount = fake()->numberBetween(1, 3);
            
            for ($i = 0; $i < $imageCount; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'filename' => 'sample_' . fake()->uuid() . '.jpg',
                    'url' => fake()->randomElement($sampleImages),
                    'order' => $i + 1,
                ]);
            }
        }
    }
}