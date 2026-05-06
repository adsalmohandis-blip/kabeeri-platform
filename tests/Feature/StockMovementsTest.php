<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Warehouse;
use App\Modules\BusinessOperations\Services\StockMovementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StockMovementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_inbound_stock_movement_increases_quantity(): void
    {
        $item = InventoryItem::factory()->create(['current_quantity' => 5]);
        $warehouse = Warehouse::factory()->create(['organization_id' => $item->organization_id]);

        $movement = app(StockMovementService::class)->record($item, 'in', 7, [
            'warehouse_id' => $warehouse->id,
        ]);

        $this->assertNotNull($movement->ulid);
        $this->assertSame('5.00', $movement->quantity_before);
        $this->assertSame('12.00', $movement->quantity_after);
        $this->assertSame('12.00', $item->refresh()->current_quantity);
    }

    public function test_outbound_stock_movement_decreases_quantity(): void
    {
        $item = InventoryItem::factory()->create(['current_quantity' => 5]);

        $movement = app(StockMovementService::class)->record($item, 'out', 2);

        $this->assertSame('-2.00', $movement->quantity);
        $this->assertSame('3.00', $item->refresh()->current_quantity);
    }

    public function test_stock_movement_cannot_go_below_zero(): void
    {
        $this->expectException(ValidationException::class);

        app(StockMovementService::class)->record(
            InventoryItem::factory()->create(['current_quantity' => 1]),
            'out',
            2
        );
    }
}
