<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Rumah', 'icon' => '🏠', 'type' => 'expense'],
            ['name' => 'Makanan', 'icon' => '🍽️', 'type' => 'expense'],
            ['name' => 'Transportasi', 'icon' => '🚗', 'type' => 'expense'],
            ['name' => 'Hiburan', 'icon' => '🎬', 'type' => 'expense'],
            ['name' => 'Belanja', 'icon' => '🛒', 'type' => 'expense'],
            ['name' => 'Lainnya', 'icon' => '📦', 'type' => 'expense'],
            ['name' => 'Gaji', 'icon' => '💰', 'type' => 'income'],
            ['name' => 'Bonus', 'icon' => '🎁', 'type' => 'income'],
            ['name' => 'Investasi', 'icon' => '📈', 'type' => 'income'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
