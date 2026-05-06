<?php

namespace Tests\Feature;

use App\Models\AgencyPartnerProfile;
use App\Models\MallMirrorProduct;
use App\Models\Package;
use App\Models\User;
use App\Modules\Rabet\Services\PartnerStorefrontService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class V4SecurityPrivacyPassTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_storefront_catalog_sharing_allows_packages_only_from_supported_catalog_sources(): void
    {
        $agency = AgencyPartnerProfile::factory()->create();
        $storefront = app(PartnerStorefrontService::class)->createDraft($agency->organization, $agency);

        $share = app(PartnerStorefrontService::class)->shareDraft($storefront, Package::factory()->create());

        $this->assertSame('draft', $share->status);
        $this->assertSame('private', $share->visibility);
    }

    public function test_partner_storefront_catalog_sharing_rejects_arbitrary_models(): void
    {
        $agency = AgencyPartnerProfile::factory()->create();
        $storefront = app(PartnerStorefrontService::class)->createDraft($agency->organization, $agency);

        $this->expectException(ValidationException::class);

        app(PartnerStorefrontService::class)->shareDraft($storefront, User::factory()->create());
    }

    public function test_partner_storefront_catalog_sharing_rejects_tenant_content_models(): void
    {
        $agency = AgencyPartnerProfile::factory()->create();
        $storefront = app(PartnerStorefrontService::class)->createDraft($agency->organization, $agency);

        $this->expectException(ValidationException::class);

        app(PartnerStorefrontService::class)->shareDraft($storefront, MallMirrorProduct::factory()->create());
    }
}
