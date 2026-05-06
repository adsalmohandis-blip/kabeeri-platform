<?php

namespace Tests\Feature;

use App\Models\AgencyPartnerProfile;
use App\Models\Organization;
use App\Models\Package;
use App\Modules\Rabet\Services\PartnerStorefrontService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PartnerStorefrontCatalogSharingDraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_storefront_starts_as_private_draft(): void
    {
        $agency = AgencyPartnerProfile::factory()->create();

        $storefront = app(PartnerStorefrontService::class)->createDraft($agency->organization, $agency, [
            'name' => 'Migration Partner Store',
            'slug' => 'migration-partner-store',
        ]);

        $this->assertDatabaseHas('partner_storefronts', [
            'id' => $storefront->id,
            'organization_id' => $agency->organization_id,
            'agency_partner_profile_id' => $agency->id,
            'status' => 'draft',
            'visibility' => 'private',
        ]);
    }

    public function test_catalog_share_starts_as_private_draft(): void
    {
        $agency = AgencyPartnerProfile::factory()->create();
        $package = Package::factory()->create();
        $service = app(PartnerStorefrontService::class);
        $storefront = $service->createDraft($agency->organization, $agency);

        $share = $service->shareDraft($storefront, $package, [
            'sort_order' => 10,
        ]);

        $this->assertDatabaseHas('partner_catalog_shares', [
            'id' => $share->id,
            'organization_id' => $agency->organization_id,
            'partner_storefront_id' => $storefront->id,
            'catalogable_type' => $package->getMorphClass(),
            'catalogable_id' => $package->id,
            'status' => 'draft',
            'visibility' => 'private',
        ]);
        $this->assertSame(10, $share->sort_order);
    }

    public function test_storefront_rejects_cross_tenant_agency(): void
    {
        $organization = Organization::factory()->create();
        $agency = AgencyPartnerProfile::factory()->create();

        $this->expectException(ValidationException::class);

        app(PartnerStorefrontService::class)->createDraft($organization, $agency);
    }
}
