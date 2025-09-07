<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        // Create main hero banner
        Banner::create([
            'title' => 'Harvviie',
            'tagline' => 'Where confidence meets elegance',
            'image' => 'https://images.pexels.com/photos/1536619/pexels-photo-1536619.jpeg',
            'position' => 1,
            'is_active' => true,
        ]);

        // Create additional promotional banners
        Banner::create([
            'title' => 'New Collection',
            'tagline' => 'Discover our latest fashion pieces',
            'image' => 'https://images.pexels.com/photos/996329/pexels-photo-996329.jpeg',
            'position' => 2,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'Custom Services',
            'tagline' => 'Personalized designs that reflect your unique style',
            'image' => 'https://images.pexels.com/photos/1043474/pexels-photo-1043474.jpeg',
            'position' => 3,
            'is_active' => true,
        ]);

        // Create additional banners using factory
        Banner::factory(3)->create();
    }
}