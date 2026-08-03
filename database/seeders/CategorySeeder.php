<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Zapatillas',
            'Sudaderas',
            'Pantalones',
            'Camisetas',
            'Chaquetas y Abrigos',
            'Chándals',
            'Bermudas y Pantalones Cortos',
            'Accesorios',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category)],
                ['name' => $category]
            );
        }
    }
}
