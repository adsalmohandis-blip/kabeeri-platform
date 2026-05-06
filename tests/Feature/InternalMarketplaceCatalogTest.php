<?php

namespace Tests\Feature;

use App\Models\MarketplaceCatalogItem;
use App\Models\Package;
use App\Models\Theme;
use App\Models\User;
use App\Modules\Core\Services\InternalMarketplaceCatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InternalMarketplaceCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_can_be_registered_and_approved_for_internal_marketplace(): void
    {
        $package = Package::factory()->create(['name' => 'Forms Pro']);
        $approver = User::factory()->create();
        $service = app(InternalMarketplaceCatalogService::class);

        $item = $service->register($package, [
            'is_featured' => true,
            'sort_order' => 5,
        ]);
        $approved = $service->approve($item, $approver);

        $this->assertSame('package', $item->item_kind);
        $this->assertSame('active', $approved->listing_status);
        $this->assertSame('approved', $approved->governance_status);
        $this->assertSame($approver->id, $approved->approved_by_user_id);
        $this->assertNotNull($approved->approved_at);
    }

    public function test_catalog_lists_only_active_approved_internal_items(): void
    {
        $active = MarketplaceCatalogItem::factory()->create([
            'listing_status' => 'active',
            'governance_status' => 'approved',
            'visibility' => 'internal',
            'is_featured' => true,
        ]);
        MarketplaceCatalogItem::factory()->create([
            'listing_status' => 'draft',
            'governance_status' => 'pending',
            'visibility' => 'internal',
        ]);

        $items = app(InternalMarketplaceCatalogService::class)->list(['item_kind' => 'package']);

        $this->assertCount(1, $items);
        $this->assertSame($active->id, $items->first()?->id);
    }

    public function test_theme_can_be_registered_as_marketplace_item(): void
    {
        $theme = Theme::query()->create([
            'name' => 'Legal Clean',
            'slug' => 'legal-clean',
            'type' => 'official',
            'status' => 'active',
        ]);

        $item = app(InternalMarketplaceCatalogService::class)->register($theme);

        $this->assertSame('theme', $item->item_kind);
        $this->assertSame($theme->getMorphClass(), $item->catalogable_type);
        $this->assertSame($theme->id, $item->catalogable_id);
    }

    public function test_unsupported_catalogable_is_rejected(): void
    {
        $this->expectException(ValidationException::class);

        app(InternalMarketplaceCatalogService::class)->register(User::factory()->create());
    }
}
