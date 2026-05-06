<?php

namespace Tests\Feature;

use App\Models\CloudSite;
use App\Models\Organization;
use App\Models\Site;
use App\Modules\Cloud\Services\CloudDomainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CloudDomainsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cloud_domain_can_be_registered_for_cloud_site(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $cloudSite = CloudSite::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
        ]);

        $domain = app(CloudDomainService::class)->register($organization, $site, 'Example.COM', [
            'cloud_site_id' => $cloudSite->id,
            'dns_records' => [['type' => 'CNAME', 'name' => 'www']],
        ]);

        $this->assertSame('example.com', $domain->domain);
        $this->assertSame($cloudSite->id, $domain->cloud_site_id);
        $this->assertSame('pending', $domain->verification_status);
        $this->assertTrue($organization->cloudDomains()->whereKey($domain->id)->exists());
        $this->assertTrue($site->cloudDomains()->whereKey($domain->id)->exists());
        $this->assertTrue($cloudSite->domains()->whereKey($domain->id)->exists());
    }

    public function test_cloud_domain_rejects_cross_tenant_site_and_cloud_site(): void
    {
        $organization = Organization::factory()->create();
        $foreignSite = Site::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $foreignCloudSite = CloudSite::factory()->create();

        $service = app(CloudDomainService::class);

        $this->expectValidationFailure(fn () => $service->register($organization, $foreignSite, 'foreign.test'));
        $this->expectValidationFailure(fn () => $service->register($organization, $site, 'foreign-cloud.test', [
            'cloud_site_id' => $foreignCloudSite->id,
        ]));
    }

    public function test_only_verified_domain_can_be_primary_and_only_one_primary_per_site(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $service = app(CloudDomainService::class);

        $first = $service->register($organization, $site, 'first.test');
        $second = $service->register($organization, $site, 'second.test');

        $this->expectValidationFailure(fn () => $service->makePrimary($first));

        $service->makePrimary($service->markVerified($first));
        $primary = $service->makePrimary($service->markVerified($second));

        $this->assertTrue($primary->is_primary);
        $this->assertFalse($first->refresh()->is_primary);
        $this->assertSame('verified', $primary->verification_status);
        $this->assertSame('ready', $primary->ssl_status);
    }

    private function expectValidationFailure(callable $callback): void
    {
        try {
            $callback();
        } catch (ValidationException) {
            $this->addToAssertionCount(1);

            return;
        }

        $this->fail('Expected validation exception.');
    }
}
