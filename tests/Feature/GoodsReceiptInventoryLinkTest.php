<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Supplier;
use App\Modules\BusinessOperations\Services\GoodsReceiptService;
use App\Modules\BusinessOperations\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GoodsReceiptInventoryLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_goods_receipt_updates_purchase_order_and_inventory(): void
    {
        $item = InventoryItem::factory()->create(['current_quantity' => 5]);
        $supplier = Supplier::factory()->create(['organization_id' => $item->organization_id]);
        $poService = app(PurchaseOrderService::class);
        $order = $poService->createDraft($supplier);
        $order = $poService->addItem($order, [
            'inventory_item_id' => $item->id,
            'name' => $item->name,
            'quantity' => 3,
            'unit_cost' => 100,
        ]);

        $receipt = app(GoodsReceiptService::class)->createDraft($order);
        $received = app(GoodsReceiptService::class)->receiveItem($receipt, $order->items()->firstOrFail(), 3);

        $this->assertSame('received', $received->status);
        $this->assertSame('8.00', $item->refresh()->current_quantity);
        $this->assertSame('received', $order->refresh()->status);
        $this->assertNotNull($received->items()->firstOrFail()->stock_movement_id);
    }

    public function test_goods_receipt_cannot_receive_more_than_ordered(): void
    {
        $poService = app(PurchaseOrderService::class);
        $order = $poService->createDraft(Supplier::factory()->create());
        $order = $poService->addItem($order, ['name' => 'Adapters', 'quantity' => 1, 'unit_cost' => 100]);
        $receipt = app(GoodsReceiptService::class)->createDraft($order);

        $this->expectException(ValidationException::class);

        app(GoodsReceiptService::class)->receiveItem($receipt, $order->items()->firstOrFail(), 2);
    }
}
