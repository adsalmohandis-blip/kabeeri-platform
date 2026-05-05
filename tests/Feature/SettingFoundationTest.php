<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Site;
use App\Modules\Core\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_set_and_get_setting_by_scope(): void
    {
        $service = app(SettingService::class);

        $service->set('platform', null, 'app.name', 'Kabeeri', 'string');

        $this->assertSame('Kabeeri', $service->get('platform', null, 'app.name'));
    }

    public function test_can_override_organization_with_site_setting(): void
    {
        $service = app(SettingService::class);
        $organization = Organization::factory()->create();
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $service->set(
            'organization',
            $organization->id,
            'theme.color',
            'amber',
            'string',
            organizationId: $organization->id,
        );

        $service->set(
            'site',
            $site->id,
            'theme.color',
            'blue',
            'string',
            organizationId: $organization->id,
        );

        $value = $service->getForScopes([
            ['scope_type' => 'site', 'scope_id' => $site->id],
            ['scope_type' => 'organization', 'scope_id' => $organization->id],
            ['scope_type' => 'platform', 'scope_id' => null],
        ], 'theme.color');

        $this->assertSame('blue', $value);
    }

    public function test_falls_back_to_organization_setting_when_site_setting_not_found(): void
    {
        $service = app(SettingService::class);
        $organization = Organization::factory()->create();
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $service->set(
            'organization',
            $organization->id,
            'timezone',
            'Africa/Cairo',
            'string',
            organizationId: $organization->id,
        );

        $value = $service->getForScopes([
            ['scope_type' => 'site', 'scope_id' => $site->id],
            ['scope_type' => 'organization', 'scope_id' => $organization->id],
        ], 'timezone');

        $this->assertSame('Africa/Cairo', $value);
    }
}
