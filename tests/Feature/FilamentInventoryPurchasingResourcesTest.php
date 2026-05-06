<?php

namespace Tests\Feature;

use App\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use App\Filament\Resources\InventoryItems\InventoryItemResource;
use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Resources\Suppliers\SupplierResource;
use App\Filament\Resources\Warehouses\WarehouseResource;
use App\Models\GoodsReceipt;
use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentInventoryPurchasingResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_access_inventory_purchasing_resource_indexes(): void
    {
        $owner = User::factory()->create();
        Organization::factory()->create(['owner_user_id' => $owner->id]);

        $this->actingAs($owner);

        $this->get(route('filament.admin.resources.inventory-items.index'))->assertOk();
        $this->get(route('filament.admin.resources.warehouses.index'))->assertOk();
        $this->get(route('filament.admin.resources.suppliers.index'))->assertOk();
        $this->get(route('filament.admin.resources.purchase-orders.index'))->assertOk();
        $this->get(route('filament.admin.resources.goods-receipts.index'))->assertOk();
    }

    public function test_inventory_purchasing_resource_queries_are_tenant_scoped(): void
    {
        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);

        $visibleItem = InventoryItem::factory()->create(['organization_id' => $organization->id]);
        $hiddenItem = InventoryItem::factory()->create();
        $visibleWarehouse = Warehouse::factory()->create(['organization_id' => $organization->id]);
        $hiddenWarehouse = Warehouse::factory()->create();
        $visibleSupplier = Supplier::factory()->create(['organization_id' => $organization->id]);
        $hiddenSupplier = Supplier::factory()->create();
        $visibleOrder = PurchaseOrder::factory()->create(['organization_id' => $organization->id]);
        $hiddenOrder = PurchaseOrder::factory()->create();
        $visibleReceipt = GoodsReceipt::factory()->create(['organization_id' => $organization->id]);
        $hiddenReceipt = GoodsReceipt::factory()->create();

        $this->actingAs($owner);

        $this->assertTrue(InventoryItemResource::getEloquentQuery()->whereKey($visibleItem->id)->exists());
        $this->assertFalse(InventoryItemResource::getEloquentQuery()->whereKey($hiddenItem->id)->exists());
        $this->assertTrue(WarehouseResource::getEloquentQuery()->whereKey($visibleWarehouse->id)->exists());
        $this->assertFalse(WarehouseResource::getEloquentQuery()->whereKey($hiddenWarehouse->id)->exists());
        $this->assertTrue(SupplierResource::getEloquentQuery()->whereKey($visibleSupplier->id)->exists());
        $this->assertFalse(SupplierResource::getEloquentQuery()->whereKey($hiddenSupplier->id)->exists());
        $this->assertTrue(PurchaseOrderResource::getEloquentQuery()->whereKey($visibleOrder->id)->exists());
        $this->assertFalse(PurchaseOrderResource::getEloquentQuery()->whereKey($hiddenOrder->id)->exists());
        $this->assertTrue(GoodsReceiptResource::getEloquentQuery()->whereKey($visibleReceipt->id)->exists());
        $this->assertFalse(GoodsReceiptResource::getEloquentQuery()->whereKey($hiddenReceipt->id)->exists());
    }
}
