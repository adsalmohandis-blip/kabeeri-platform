<?php

namespace Database\Factories;

use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ProductOptionValue> */
class ProductOptionValueFactory extends Factory
{
    protected $model = ProductOptionValue::class;

    public function definition(): array
    {
        $value = fake()->randomElement(['Small', 'Medium', 'Large']);

        return [
            'product_option_id' => ProductOption::factory(),
            'value' => $value,
            'slug' => Str::slug($value).'-'.fake()->unique()->numberBetween(1000, 9999),
            'sort_order' => 0,
        ];
    }
}
