<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CmsMenuFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_can_have_tenant_scoped_menus(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $menu = Menu::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Main Navigation',
            'slug' => 'main-navigation',
            'location' => 'header',
        ]);

        $this->assertSame($organization->id, $menu->organization->id);
        $this->assertSame($site->id, $menu->site->id);
        $this->assertSame('active', $menu->status);
        $this->assertSame(['source' => 'factory'], $menu->metadata);
        $this->assertTrue($site->menus()->whereKey($menu->id)->exists());
        $this->assertTrue($organization->menus()->whereKey($menu->id)->exists());
    }

    public function test_menu_can_have_nested_items(): void
    {
        $menu = Menu::factory()->create();

        $parent = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => 'Services',
            'url' => '/services',
            'sort_order' => 10,
        ]);

        $child = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'label' => 'Consulting',
            'url' => '/services/consulting',
            'sort_order' => 20,
        ]);

        $this->assertTrue($menu->items()->whereKey($parent->id)->exists());
        $this->assertTrue($menu->items()->whereKey($child->id)->exists());
        $this->assertSame($parent->id, $child->parent->id);
        $this->assertTrue($parent->children()->whereKey($child->id)->exists());
        $this->assertTrue($menu->rootItems()->whereKey($parent->id)->exists());
        $this->assertFalse($menu->rootItems()->whereKey($child->id)->exists());
    }

    public function test_menu_slug_is_unique_per_site(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $otherSite = Site::factory()->create(['organization_id' => $organization->id]);

        Menu::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'slug' => 'main',
        ]);

        Menu::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $otherSite->id,
            'slug' => 'main',
        ]);

        $this->expectException(ValidationException::class);

        validator([
            'site_id' => $site->id,
            'slug' => 'main',
        ], [
            'slug' => ['unique:menus,slug,NULL,id,site_id,'.$site->id],
        ])->validate();
    }
}
