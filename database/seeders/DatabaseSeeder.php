<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        // 1. Categorías
        $categoriesNames = ['Zapatillas', 'Sudaderas', 'Pantalones'];
        $categories = [];

        foreach ($categoriesNames as $name) {
            $categories[] = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }

        // 2. Productos con variantes de tallas
        $totalProducts = random_int(10, 15);

        $shoesSizes  = ['38', '39', '40', '41', '42', '43', '44'];
        $clothesSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

        for ($i = 0; $i < $totalProducts; $i++) {
            $randomCategory = $categories[array_rand($categories)];

            $product = Product::factory()->create([
                'category_id' => $randomCategory->id,
            ]);

            // Elegir pool de tallas según categoría
            $sizesPool = ($randomCategory->slug === 'zapatillas') ? $shoesSizes : $clothesSizes;

            // Tomar entre 3 y 5 tallas aleatorias sin repetir
            $selectedSizes = array_slice(
                array_unique(fake()->randomElements($sizesPool, rand(3, 5))),
                0
            );

            foreach ($selectedSizes as $size) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size'       => $size,
                    'color'      => null,
                    'stock'      => rand(0, 10),
                ]);
            }
        }

        Model::reguard();
    }
}

