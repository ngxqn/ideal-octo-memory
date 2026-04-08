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
            ['name' => 'Bánh Ngọt', 'slug' => 'ngot'],
            ['name' => 'Bánh Mặn', 'slug' => 'man'],
            ['name' => 'Bánh Chay', 'slug' => 'chay'],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(['name' => $c['name']], $c);
        }
    }
}
