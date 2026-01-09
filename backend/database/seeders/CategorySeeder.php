<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Discover local fashion boutiques and clothing stores',
                'icon' => '👗',
                'is_active' => true,
            ],
            [
                'name' => 'Restaurant',
                'slug' => 'restaurant',
                'description' => 'Find the best local restaurants and dining experiences',
                'icon' => '🍽️',
                'is_active' => true,
            ],
            [
                'name' => 'Cafe',
                'slug' => 'cafe',
                'description' => 'Explore cozy cafes and coffee shops',
                'icon' => '☕',
                'is_active' => true,
            ],
            [
                'name' => 'Beauty & Spa',
                'slug' => 'beauty-spa',
                'description' => 'Beauty salons, spas, and wellness centers',
                'icon' => '💅',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
