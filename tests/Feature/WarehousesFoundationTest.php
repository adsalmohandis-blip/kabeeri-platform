<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Warehouse;
use App\Modules\BusinessOperations\Services\WarehouseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehousesFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouse_can_be_created_for_organization(): void
    {
        $organization = Organization::factory()->create();

        $warehouse = app(WarehouseService::class)->createForOrganization($organization, [
            'name' => 'Main Warehouse',
            'slug' => 'main-warehouse',
            'code' => 'MAIN',
            'is_default' => true,
        ]);

        $this->assertNotNull($warehouse->ulid);
        $this->assertTrue($warehouse->organization->is($organization));
        $this->assertTrue($warehouse->is_default);
    }

    public function test_only_one_default_warehouse_per_organization(): void
    {
        $organization = Organization::factory()->create();
        Warehouse::factory()->create([
            'organization_id' => $organization->id,
            'is_default' => true,
        ]);
        $second = Warehouse::factory()->create([
            'organization_id' => $organization->id,
            'is_default' => false,
        ]);

        app(WarehouseService::class)->makeDefault($second);

        $this->assertSame(1, Warehouse::query()
            ->where('organization_id', $organization->id)
            ->where('is_default', true)
            ->count());
        $this->assertTrue($second->refresh()->is_default);
    }
}
