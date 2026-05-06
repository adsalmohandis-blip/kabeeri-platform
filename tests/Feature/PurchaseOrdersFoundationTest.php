<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Modules\BusinessOperations\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PurchaseOrdersFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_order_can_be_created_for_supplier(): void
    {
        $supplier = Supplier::factory()->create();
        $warehouse = Warehouse::factory()->create(['organization_id' => $supplier->organization_id]);

        $order = app(PurchaseOrderService::class)->createDraft($supplier, [
            'warehouse_id' => $warehouse->id,
        ]);

        $this->assertNotNull($order->ulid);
        $this->assertSame('PO-00001', $order->purchase_order_number);
        $this->assertTrue($order->supplier->is($supplier));
        $this->assertTrue($order->warehouse->is($warehouse));
    }

    public function test_purchase_order_items_recalculate_totals(): void
    {
        $service = app(PurchaseOrderService::class);
        $order = $service->createDraft(Supplier::factory()->create());

        $updated = $service->addItem($order, [
            'name' => 'Adapters',
            'quantity' => 3,
            'unit_cost' => 100,
            'tax_total' => 30,
        ]);

        $this->assertSame('300.00', $updated->subtotal);
        $this->assertSame('30.00', $updated->tax_total);
        $this->assertSame('330.00', $updated->total);
    }

    public function test_purchase_order_rejects_other_organization_inventory_item(): void
    {
        $this->expectException(ValidationException::class);

        app(PurchaseOrderService::class)->addItem(
            app(PurchaseOrderService::class)->createDraft(Supplier::factory()->create()),
            [
                'inventory_item_id' => InventoryItem::factory()->create()->id,
                'name' => 'Wrong tenant item',
            ]
        );
    }
}
