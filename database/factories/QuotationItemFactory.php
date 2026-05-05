<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuotationItem>
 */
class QuotationItemFactory extends Factory
{
    protected $model = QuotationItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 100, 1000);

        return [
            'quotation_id' => Quotation::factory(),
            'product_id' => fake()->boolean(20) ? Product::factory() : null,
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_total' => 0,
            'tax_total' => 0,
            'total' => $quantity * $unitPrice,
            'sort_order' => 0,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
