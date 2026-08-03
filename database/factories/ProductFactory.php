<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $price = $this->faker->randomFloat(2, 15, 60);
        $hasDiscount = $this->faker->boolean(70);
        $originalPrice = $hasDiscount ? $price + $this->faker->randomFloat(2, 5, 30) : null;

        return [
            'name'           => ucfirst($name),
            'slug'           => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'description'    => $this->faker->paragraph(),
            'price'          => $price,
            'original_price' => $originalPrice,
            'is_active'      => true,
            'image_path'     => null,
        ];
    }
}
