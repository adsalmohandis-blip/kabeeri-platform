<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\PackageRegistryService;
use Database\Seeders\ModulesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_modules_seeder_adds_v1_modules(): void
    {
        $this->seed(ModulesSeeder::class);

        $this->assertDatabaseHas('modules', ['key' => 'core', 'module_type' => 'core']);
        $this->assertDatabaseHas('modules', ['key' => 'cms', 'module_type' => 'cms']);
        $this->assertDatabaseHas('modules', ['key' => 'media', 'module_type' => 'cms']);
        $this->assertDatabaseHas('modules', ['key' => 'rabet', 'module_type' => 'industry']);
    }

    public function test_can_enable_module_for_organization_and_site(): void
    {
        $this->seed(ModulesSeeder::class);

        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $installer = User::factory()->create();
        $service = app(PackageRegistryService::class);

        $installation = $service->enableModuleByKey(
            organizationId: $organization->id,
            moduleKey: 'cms',
            siteId: $site->id,
            installedBy: $installer->id,
            settings: ['enabled_features' => ['pages', 'posts']],
        );

        $cmsModule = Module::query()->where('key', 'cms')->firstOrFail();

        $this->assertDatabaseHas('module_installations', [
            'id' => $installation->id,
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'module_id' => $cmsModule->id,
            'status' => 'active',
        ]);

        $this->assertTrue(
            $service->isEnabled(
                organizationId: $organization->id,
                moduleKey: 'cms',
                siteId: $site->id,
            ),
        );
    }
}
