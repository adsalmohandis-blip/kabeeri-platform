<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'organization_id' => Organization::factory(),
            'site_id' => null,
            'company_id' => null,
            'category_id' => ProductCategory::factory()->state(fn (array $attributes): array => [
                'organization_id' => $attributes['organization_id'],
                'site_id' => $attributes['site_id'] ?? null,
            ]),
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'status' => 'draft',
            'visibility' => 'public',
            'price' => fake()->randomFloat(2, 10, 1000),
            'currency_code' => 'USD',
            'sku' => fake()->unique()->bothify('SKU-####'),
            'stock_status' => 'in_stock',
            'seo' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
