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
            ['name' => 'Bánh Ngọt'],
            ['name' => 'Bánh Mặn'],
            ['name' => 'Bánh Chay'],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(['name' => $c['name']], $c);
        }
    }
}
