<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Bánh ngọt', 'slug' => 'ngot'],
            ['name' => 'Bánh mặn', 'slug' => 'man'],
            ['name' => 'Bánh chay', 'slug' => 'chay'],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(['name' => $c['name']], $c);
        }
    }
}
