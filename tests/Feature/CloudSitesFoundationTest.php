<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Site;
use App\Modules\Cloud\Services\CloudSiteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CloudSitesFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cloud_site_can_be_registered_for_site_without_external_provisioning(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $cloudSite = app(CloudSiteService::class)->createForSite($organization, $site, [
            'provider' => 'manual',
            'region' => 'local',
            'settings' => ['provisioning' => 'record_only'],
        ]);

        $this->assertSame($organization->id, $cloudSite->organization_id);
        $this->assertSame($site->id, $cloudSite->site_id);
        $this->assertSame('draft', $cloudSite->deployment_status);
        $this->assertSame('unknown', $cloudSite->health_status);
        $this->assertTrue($organization->cloudSites()->whereKey($cloudSite->id)->exists());
        $this->assertTrue($site->cloudSites()->whereKey($cloudSite->id)->exists());
    }

    public function test_cloud_site_rejects_site_from_other_organization(): void
    {
        $organization = Organization::factory()->create();
        $foreignSite = Site::factory()->create();

        $this->expectException(ValidationException::class);

        app(CloudSiteService::class)->createForSite($organization, $foreignSite);
    }

    public function test_cloud_site_status_can_be_updated_safely(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $service = app(CloudSiteService::class);

        $cloudSite = $service->createForSite($organization, $site);
        $deploying = $service->markDeploying($cloudSite);
        $this->assertSame('deploying', $deploying->deployment_status);

        $deployed = $service->markDeployed($deploying, 'https://demo.test');
        $this->assertSame('deployed', $deployed->deployment_status);
        $this->assertSame('healthy', $deployed->health_status);
        $this->assertSame('https://demo.test', $deployed->public_url);

        $unhealthy = $service->markUnhealthy($deployed, 'Health check timeout');

        $this->assertSame('unhealthy', $unhealthy->health_status);
        $this->assertSame('Health check timeout', $unhealthy->metadata['last_health_warning']);
    }
}
