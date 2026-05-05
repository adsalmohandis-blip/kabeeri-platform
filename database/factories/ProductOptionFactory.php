<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductOption> */
class ProductOptionFactory extends Factory
{
    protected $model = ProductOption::class;

    public function definition(): array
    {
        $name = fake()->randomElement(['Size', 'Color', 'Material']);

        return [
            'product_id' => Product::factory(),
            'name' => $name,
            'slug' => strtolower($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'sort_order' => 0,
        ];
    }
}
