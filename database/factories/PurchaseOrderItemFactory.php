<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitCost = fake()->randomFloat(2, 50, 500);

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'inventory_item_id' => null,
            'name' => fake()->words(3, true),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'tax_total' => 0,
            'total' => $quantity * $unitCost,
            'received_quantity' => 0,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
