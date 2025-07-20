<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $name,
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 2000),
            'stock_quantity' => $this->faker->numberBetween(0, 500),
            'meta_key' => $this->faker->words(3, true),
            'meta_description' => $this->faker->sentence(),
        ];
    }
}
