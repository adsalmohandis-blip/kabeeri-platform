<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Product;
use App\Models\Site;
use App\Models\User;
use App\Modules\Mall\Services\MallPublicationConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MallPublicationConsentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_publication_consent_starts_pending_and_requires_grant(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $product = Product::factory()->create(['organization_id' => $organization->id, 'site_id' => $site->id]);
        $user = User::factory()->create();
        $service = app(MallPublicationConsentService::class);

        $consent = $service->request($organization, $site, $product, ['products']);

        $this->assertSame('pending', $consent->status);
        $this->assertFalse($service->allowsPublication($consent, 'products'));

        $granted = $service->grant($consent, $user);

        $this->assertSame('granted', $granted->status);
        $this->assertSame($user->id, $granted->granted_by);
        $this->assertTrue($service->allowsPublication($granted, 'products'));
        $this->assertFalse($service->allowsPublication($granted, 'services'));
        $this->assertTrue($organization->mallPublicationConsents()->whereKey($granted->id)->exists());
        $this->assertTrue($site->mallPublicationConsents()->whereKey($granted->id)->exists());
    }

    public function test_revoked_consent_does_not_allow_publication(): void
    {
        $organization = Organization::factory()->create();
        $service = app(MallPublicationConsentService::class);
        $consent = $service->grant($service->request($organization, channels: ['business_directory']), User::factory()->create());

        $revoked = $service->revoke($consent);

        $this->assertSame('revoked', $revoked->status);
        $this->assertFalse($service->allowsPublication($revoked, 'business_directory'));
        $this->assertNotNull($revoked->revoked_at);
    }

    public function test_consent_rejects_cross_tenant_site_or_subject(): void
    {
        $organization = Organization::factory()->create();
        $foreignSite = Site::factory()->create();
        $foreignProduct = Product::factory()->create();
        $service = app(MallPublicationConsentService::class);

        $this->expectValidationFailure(fn () => $service->request($organization, $foreignSite));
        $this->expectValidationFailure(fn () => $service->request($organization, subject: $foreignProduct));
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
