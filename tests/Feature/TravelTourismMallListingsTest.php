<?php

namespace Tests\Feature;

use App\Models\MallPublicationConsent;
use App\Models\Organization;
use App\Models\TravelTourismMallListing;
use App\Models\User;
use App\Modules\Mall\Services\MallPublicationConsentService;
use App\Modules\Mall\Services\TravelTourismMallListingService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TravelTourismMallListingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_travel_tourism_listing_can_be_created_as_draft(): void
    {
        $organization = Organization::factory()->create();

        $listing = app(TravelTourismMallListingService::class)->createDraft($organization, [
            'title' => 'Cairo Weekend Tour',
            'slug' => 'cairo-weekend-tour',
            'listing_type' => 'tour',
            'destination' => 'Cairo',
            'price_from' => 120,
        ]);

        $this->assertSame('Cairo Weekend Tour', $listing->title);
        $this->assertSame('tour', $listing->listing_type);
        $this->assertSame('draft', $listing->listing_status);
        $this->assertSame('120.00', $listing->price_from);
        $this->assertTrue($organization->travelTourismMallListings()->whereKey($listing->id)->exists());
    }

    public function test_travel_tourism_listing_requires_granted_consent_before_publish(): void
    {
        $organization = Organization::factory()->create();
        $service = app(TravelTourismMallListingService::class);
        $listing = $service->createDraft($organization, [
            'title' => 'Red Sea Package',
            'slug' => 'red-sea-package',
            'listing_type' => 'package',
        ]);

        $this->expectValidationFailure(fn () => $service->publish($listing));

        $consent = app(MallPublicationConsentService::class)->request($organization, channels: ['travel_tourism']);
        app(MallPublicationConsentService::class)->grant($consent, User::factory()->create());

        $listing = $service->createDraft($organization, [
            'title' => 'Red Sea Package',
            'slug' => 'red-sea-package',
            'listing_type' => 'package',
        ], $consent);
        $published = $service->publish($listing);

        $this->assertSame('published', $published->listing_status);
        $this->assertNotNull($published->published_at);
    }

    public function test_travel_tourism_listing_can_be_unpublished(): void
    {
        $organization = Organization::factory()->create();
        $consent = app(MallPublicationConsentService::class)->grant(
            app(MallPublicationConsentService::class)->request($organization, channels: ['travel_tourism']),
            User::factory()->create(),
        );
        $service = app(TravelTourismMallListingService::class);
        $listing = $service->publish($service->createDraft($organization, [
            'title' => 'Luxor Day Trip',
            'slug' => 'luxor-day-trip',
        ], $consent));

        $unpublished = $service->unpublish($listing);

        $this->assertSame('unpublished', $unpublished->listing_status);
        $this->assertNull($unpublished->published_at);
    }

    public function test_travel_tourism_listing_rejects_cross_tenant_consent(): void
    {
        $organization = Organization::factory()->create();
        $foreignConsent = MallPublicationConsent::factory()->create();

        $this->expectException(ValidationException::class);

        app(TravelTourismMallListingService::class)->createDraft($organization, [
            'title' => 'Foreign Consent',
            'slug' => 'foreign-consent',
        ], $foreignConsent);
    }

    public function test_travel_tourism_listing_slug_is_unique_per_organization(): void
    {
        $organization = Organization::factory()->create();

        TravelTourismMallListing::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'unique-travel',
        ]);

        $this->expectException(QueryException::class);

        TravelTourismMallListing::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'unique-travel',
        ]);
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
