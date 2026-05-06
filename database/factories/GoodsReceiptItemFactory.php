<?php

namespace Database\Factories;

use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GoodsReceiptItem>
 */
class GoodsReceiptItemFactory extends Factory
{
    protected $model = GoodsReceiptItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderItem = PurchaseOrderItem::factory()->create();

        return [
            'goods_receipt_id' => GoodsReceipt::factory()->create([
                'organization_id' => $orderItem->purchaseOrder->organization_id,
                'purchase_order_id' => $orderItem->purchase_order_id,
            ])->id,
            'purchase_order_item_id' => $orderItem->id,
            'inventory_item_id' => $orderItem->inventory_item_id,
            'stock_movement_id' => null,
            'quantity' => 1,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
