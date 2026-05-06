<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\DashboardWidget;
use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\Supplier;
use Database\Seeders\V1DemoSeeder;
use Database\Seeders\V3DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V3DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_v3_demo_seeder_is_idempotent(): void
    {
        $this->seed(V1DemoSeeder::class);
        $this->seed(V3DemoSeeder::class);
        $this->seed(V3DemoSeeder::class);

        $organization = Organization::query()->firstOrFail();

        $this->assertSame(1, Contact::query()->where('organization_id', $organization->id)->where('email', 'v3-demo-contact@example.com')->count());
        $this->assertSame(1, InventoryItem::query()->where('organization_id', $organization->id)->where('sku', 'V3-DEMO-ITEM')->count());
        $this->assertSame(1, Supplier::query()->where('organization_id', $organization->id)->where('slug', 'v3-demo-supplier')->count());
        $this->assertSame(1, DashboardWidget::query()->where('organization_id', $organization->id)->where('key', 'v3-demo-widget')->count());
    }
}
