<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $supplier = Supplier::factory()->create();

        return [
            'organization_id' => $supplier->organization_id,
            'supplier_id' => $supplier->id,
            'warehouse_id' => null,
            'purchase_order_number' => 'PO-'.fake()->unique()->numberBetween(1000, 9999),
            'status' => 'draft',
            'currency_code' => 'EGP',
            'subtotal' => 0,
            'tax_total' => 0,
            'total' => 0,
            'ordered_at' => null,
            'expected_at' => null,
            'notes' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
