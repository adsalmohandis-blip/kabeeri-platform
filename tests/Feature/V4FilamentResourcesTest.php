<?php

namespace Tests\Feature;

use App\Filament\Resources\AgencyPartnerProfiles\AgencyPartnerProfileResource;
use App\Filament\Resources\MarketplaceCatalogItems\MarketplaceCatalogItemResource;
use App\Filament\Resources\ModerationCases\ModerationCaseResource;
use App\Filament\Resources\PartnerStorefronts\PartnerStorefrontResource;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\AgencyPartnerProfile;
use App\Models\MarketplaceCatalogItem;
use App\Models\ModerationCase;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\PartnerStorefront;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V4FilamentResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_v4_tenant_scoped_resources_only_return_accessible_organization_records(): void
    {
        [$user, $organization] = $this->ownedOrganization();
        $case = ModerationCase::factory()->create(['organization_id' => $organization->id]);
        $review = Review::factory()->create(['organization_id' => $organization->id]);
        $agency = AgencyPartnerProfile::factory()->create(['organization_id' => $organization->id]);
        $storefront = PartnerStorefront::factory()->create(['organization_id' => $organization->id]);

        ModerationCase::factory()->create();
        Review::factory()->create();
        AgencyPartnerProfile::factory()->create();
        PartnerStorefront::factory()->create();

        $this->actingAs($user);

        $this->assertSame([$case->id], ModerationCaseResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$review->id], ReviewResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$agency->id], AgencyPartnerProfileResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$storefront->id], PartnerStorefrontResource::getEloquentQuery()->pluck('id')->all());
    }

    public function test_v4_resources_return_no_records_when_unauthenticated(): void
    {
        ModerationCase::factory()->create();
        MarketplaceCatalogItem::factory()->create();

        $this->assertSame(0, ModerationCaseResource::getEloquentQuery()->count());
        $this->assertSame(0, MarketplaceCatalogItemResource::getEloquentQuery()->count());
    }

    public function test_v4_resource_pages_are_registered(): void
    {
        $this->assertArrayHasKey('index', ModerationCaseResource::getPages());
        $this->assertArrayHasKey('index', ReviewResource::getPages());
        $this->assertArrayHasKey('index', MarketplaceCatalogItemResource::getPages());
        $this->assertArrayHasKey('index', AgencyPartnerProfileResource::getPages());
        $this->assertArrayHasKey('index', PartnerStorefrontResource::getPages());
    }

    /**
     * @return array{0: User, 1: Organization}
     */
    private function ownedOrganization(): array
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $user->id]);

        OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        return [$user, $organization];
    }
}
