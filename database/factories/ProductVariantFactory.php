<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductVariant> */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => fake()->unique()->bothify('VAR-####'),
            'price' => fake()->randomFloat(2, 10, 1000),
            'stock_status' => 'in_stock',
            'option_values' => ['size' => 'medium'],
            'status' => 'active',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
