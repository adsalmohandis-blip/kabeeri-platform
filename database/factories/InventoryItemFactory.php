<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'site_id' => null,
            'product_id' => null,
            'name' => fake()->words(3, true),
            'sku' => 'SKU-'.fake()->unique()->numberBetween(10000, 99999),
            'item_type' => 'stocked',
            'unit_of_measure' => 'pcs',
            'status' => 'active',
            'cost_price' => fake()->randomFloat(2, 50, 500),
            'sale_price' => fake()->randomFloat(2, 100, 1000),
            'reorder_level' => 5,
            'current_quantity' => fake()->randomFloat(2, 0, 100),
            'metadata' => ['source' => 'factory'],
        ];
    }

    public function forProduct(?Product $product = null): self
    {
        return $this->state(function () use ($product): array {
            $product ??= Product::factory()->create();

            return [
                'organization_id' => $product->organization_id,
                'company_id' => $product->company_id,
                'site_id' => $product->site_id,
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'sale_price' => $product->price,
            ];
        });
    }
}
