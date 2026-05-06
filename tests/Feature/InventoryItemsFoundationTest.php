<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\Product;
use App\Modules\BusinessOperations\Services\InventoryItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryItemsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_item_can_be_created_for_organization(): void
    {
        $organization = Organization::factory()->create();

        $item = app(InventoryItemService::class)->createForOrganization($organization, [
            'name' => 'Spare adapter',
            'sku' => 'ADP-001',
            'reorder_level' => 10,
            'current_quantity' => 8,
        ]);

        $this->assertNotNull($item->ulid);
        $this->assertTrue($item->organization->is($organization));
        $this->assertSame('active', $item->status);
        $this->assertTrue($item->needsReorder());
    }

    public function test_inventory_item_can_link_to_product_in_same_organization(): void
    {
        $product = Product::factory()->create([
            'sku' => 'PROD-001',
            'price' => 1200,
        ]);

        $item = InventoryItem::factory()->forProduct($product)->create();

        $this->assertTrue($item->product->is($product));
        $this->assertSame($product->organization_id, $item->organization_id);
        $this->assertSame('1200.00', $item->sale_price);
    }

    public function test_inventory_item_rejects_product_from_other_organization(): void
    {
        $this->expectException(ValidationException::class);

        app(InventoryItemService::class)->createForOrganization(Organization::factory()->create(), [
            'product_id' => Product::factory()->create()->id,
            'name' => 'Wrong tenant item',
        ]);
    }
}
