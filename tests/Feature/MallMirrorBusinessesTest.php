<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\MallPublicationConsent;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Mall\Services\MallMirrorBusinessService;
use App\Modules\Mall\Services\MallPublicationConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MallMirrorBusinessesTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_profile_can_be_mirrored_as_draft(): void
    {
        $profile = BusinessProfile::factory()->create([
            'public_email' => 'hello@example.test',
            'public_phone' => '123',
            'website_url' => 'https://example.test',
        ]);

        $mirror = app(MallMirrorBusinessService::class)->createDraft($profile);

        $this->assertSame($profile->organization_id, $mirror->organization_id);
        $this->assertSame($profile->id, $mirror->business_profile_id);
        $this->assertSame('draft', $mirror->mirror_status);
        $this->assertSame('hello@example.test', $mirror->public_contacts['email']);
        $this->assertTrue($profile->mallMirrorBusinesses()->whereKey($mirror->id)->exists());
        $this->assertTrue($profile->organization->mallMirrorBusinesses()->whereKey($mirror->id)->exists());
    }

    public function test_business_mirror_requires_granted_consent_before_publish(): void
    {
        $profile = BusinessProfile::factory()->create();
        $service = app(MallMirrorBusinessService::class);
        $mirror = $service->createDraft($profile);

        $this->expectValidationFailure(fn () => $service->publish($mirror));

        $consent = app(MallPublicationConsentService::class)->request(
            $profile->organization,
            subject: $profile,
            channels: ['business_directory'],
        );
        app(MallPublicationConsentService::class)->grant($consent, User::factory()->create());

        $mirror = $service->createDraft($profile, $consent);
        $published = $service->publish($mirror);

        $this->assertSame('published', $published->mirror_status);
        $this->assertNotNull($published->published_at);
    }

    public function test_business_mirror_rejects_cross_tenant_consent(): void
    {
        $profile = BusinessProfile::factory()->create();
        $foreignConsent = MallPublicationConsent::factory()->create([
            'organization_id' => Organization::factory()->create()->id,
        ]);

        $this->expectException(ValidationException::class);

        app(MallMirrorBusinessService::class)->createDraft($profile, $foreignConsent);
    }

    public function test_business_mirror_can_be_unpublished(): void
    {
        $profile = BusinessProfile::factory()->create();
        $consent = app(MallPublicationConsentService::class)->grant(
            app(MallPublicationConsentService::class)->request($profile->organization, subject: $profile, channels: ['business_directory']),
            User::factory()->create(),
        );
        $service = app(MallMirrorBusinessService::class);
        $published = $service->publish($service->createDraft($profile, $consent));

        $unpublished = $service->unpublish($published);

        $this->assertSame('unpublished', $unpublished->mirror_status);
        $this->assertNull($unpublished->published_at);
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
