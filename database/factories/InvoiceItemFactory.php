<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

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
            'invoice_id' => Invoice::factory(),
            'product_id' => null,
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
