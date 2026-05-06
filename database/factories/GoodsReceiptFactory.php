<?php

namespace Database\Factories;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GoodsReceipt>
 */
class GoodsReceiptFactory extends Factory
{
    protected $model = GoodsReceipt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $order = PurchaseOrder::factory()->create();

        return [
            'organization_id' => $order->organization_id,
            'purchase_order_id' => $order->id,
            'warehouse_id' => $order->warehouse_id,
            'receipt_number' => 'GR-'.fake()->unique()->numberBetween(1000, 9999),
            'status' => 'draft',
            'received_at' => null,
            'notes' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
