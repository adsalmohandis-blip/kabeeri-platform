<?php

namespace Tests\Feature;

use App\Models\MallMirrorService;
use App\Models\MallPublicationConsent;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Mall\Services\MallMirrorServiceCatalogService;
use App\Modules\Mall\Services\MallPublicationConsentService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MallMirrorServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_mall_mirror_service_can_be_created(): void
    {
        $organization = Organization::factory()->create();

        $mirrorService = app(MallMirrorServiceCatalogService::class)->createDraft($organization, [
            'service_name' => 'Web Development',
            'slug' => 'web-development',
            'hourly_rate' => 150.00,
            'service_category' => 'Development',
        ]);

        $this->assertSame('Web Development', $mirrorService->service_name);
        $this->assertSame('web-development', $mirrorService->slug);
        $this->assertSame('150.00', $mirrorService->hourly_rate);
        $this->assertSame('draft', $mirrorService->mirror_status);
        $this->assertTrue($organization->mallMirrorServices()->whereKey($mirrorService->id)->exists());
    }

    public function test_mirror_service_requires_granted_services_consent_before_publish(): void
    {
        $organization = Organization::factory()->create();
        $service = app(MallMirrorServiceCatalogService::class);
        $mirrorService = $service->createDraft($organization, [
            'service_name' => 'Consulting',
            'slug' => 'consulting',
        ]);

        $this->expectValidationFailure(fn () => $service->publish($mirrorService));

        $consent = app(MallPublicationConsentService::class)->request($organization, channels: ['services']);
        app(MallPublicationConsentService::class)->grant($consent, User::factory()->create());

        $mirrorService = $service->createDraft($organization, [
            'service_name' => 'Consulting',
            'slug' => 'consulting',
        ], $consent);
        $published = $service->publish($mirrorService);

        $this->assertSame('published', $published->mirror_status);
        $this->assertNotNull($published->published_at);
    }

    public function test_mirror_service_can_be_unpublished(): void
    {
        $organization = Organization::factory()->create();
        $consent = app(MallPublicationConsentService::class)->grant(
            app(MallPublicationConsentService::class)->request($organization, channels: ['services']),
            User::factory()->create(),
        );
        $service = app(MallMirrorServiceCatalogService::class);
        $mirrorService = $service->publish($service->createDraft($organization, [
            'service_name' => 'Support',
            'slug' => 'support',
        ], $consent));

        $unpublished = $service->unpublish($mirrorService);

        $this->assertSame('unpublished', $unpublished->mirror_status);
        $this->assertNull($unpublished->published_at);
    }

    public function test_mirror_service_with_consent(): void
    {
        $organization = Organization::factory()->create();
        $consent = MallPublicationConsent::factory()->create(['organization_id' => $organization->id]);

        $mirrorService = MallMirrorService::factory()->create([
            'organization_id' => $organization->id,
            'mall_publication_consent_id' => $consent->id,
        ]);

        $this->assertInstanceOf(MallPublicationConsent::class, $mirrorService->publicationConsent);
    }

    public function test_mirror_service_rejects_cross_tenant_consent(): void
    {
        $organization = Organization::factory()->create();
        $foreignConsent = MallPublicationConsent::factory()->create([
            'organization_id' => Organization::factory()->create()->id,
        ]);

        $this->expectException(ValidationException::class);

        app(MallMirrorServiceCatalogService::class)->createDraft($organization, [
            'service_name' => 'Foreign Consent',
            'slug' => 'foreign-consent',
        ], $foreignConsent);
    }

    public function test_mirror_service_slug_is_unique_per_organization(): void
    {
        $organization = Organization::factory()->create();

        MallMirrorService::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'unique-slug',
        ]);

        $this->expectException(QueryException::class);

        MallMirrorService::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'unique-slug',
        ]);
    }

    public function test_mirror_service_can_be_soft_deleted(): void
    {
        $mirrorService = MallMirrorService::factory()->create();

        $this->assertNull($mirrorService->deleted_at);

        $mirrorService->delete();

        $this->assertNotNull($mirrorService->refresh()->deleted_at);
        $this->assertEmpty(MallMirrorService::all());
        $this->assertNotEmpty(MallMirrorService::withTrashed()->get());
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
