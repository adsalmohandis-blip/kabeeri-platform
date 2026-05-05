<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PluginBundle;
use App\Models\PluginBundleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginBundlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_bundle_can_contain_packages(): void
    {
        $bundle = PluginBundle::factory()->create(['slug' => 'business-starter']);
        $required = Package::factory()->create(['key' => 'kabeeri.cms']);
        $recommended = Package::factory()->create(['key' => 'kabeeri.forms']);

        PluginBundleItem::factory()->create([
            'plugin_bundle_id' => $bundle->id,
            'package_id' => $required->id,
            'requirement_level' => 'required',
            'sort_order' => 10,
        ]);
        PluginBundleItem::factory()->create([
            'plugin_bundle_id' => $bundle->id,
            'package_id' => $recommended->id,
            'requirement_level' => 'recommended',
            'sort_order' => 20,
        ]);

        $this->assertSame(['kabeeri.cms', 'kabeeri.forms'], $bundle->items->pluck('package.key')->all());
        $this->assertSame('required', $bundle->items->first()->requirement_level);
    }
}
