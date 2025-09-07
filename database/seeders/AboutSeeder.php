<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\User;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        About::create([
            'content' => 'Founded in South Africa in 2023 by Harvey, Harvviie was born from a vision of confidence, elegance, and artistic expression.

Inspired by the timeless sophistication of 1980s old money fashion, we craft clothing that empowers all genders, cultures, and body types.

At Harvviie, fashion is more than clothing—it\'s about presence, confidence, and the art of being authentically you',
            'milestones' => [
                [
                    'year' => 2023,
                    'title' => 'Harvviie Founded',
                    'description' => 'Harvey founded Harvviie with a vision of confidence and elegance in fashion.',
                ],
                [
                    'year' => 2023,
                    'title' => 'First Collection Launch',
                    'description' => 'Launched our first collection inspired by 1980s old money fashion.',
                ],
                [
                    'year' => 2024,
                    'title' => 'Custom Services',
                    'description' => 'Introduced personalized design and custom branding services.',
                ],
                [
                    'year' => 2024,
                    'title' => 'Growing Community',
                    'description' => 'Built a loyal community of customers across South Africa.',
                ],
            ],
            'updated_by_user_id' => $admin->id,
        ]);
    }
}