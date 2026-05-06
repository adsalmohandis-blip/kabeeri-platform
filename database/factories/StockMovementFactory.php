<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $item = InventoryItem::factory()->create();
        $quantity = fake()->randomFloat(2, 1, 10);

        return [
            'organization_id' => $item->organization_id,
            'warehouse_id' => Warehouse::factory()->create(['organization_id' => $item->organization_id])->id,
            'inventory_item_id' => $item->id,
            'movement_type' => 'adjustment',
            'quantity' => $quantity,
            'quantity_before' => $item->current_quantity,
            'quantity_after' => (float) $item->current_quantity + $quantity,
            'reference_type' => null,
            'reference_id' => null,
            'notes' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
