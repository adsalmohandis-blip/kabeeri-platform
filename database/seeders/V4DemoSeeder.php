<?php

namespace Database\Seeders;

use App\Models\AcademyBadge;
use App\Models\AcademyBadgeAward;
use App\Models\AgencyDashboardSnapshot;
use App\Models\AgencyPartnerProfile;
use App\Models\BusinessProfile;
use App\Models\Company;
use App\Models\CreatorProfile;
use App\Models\GrowthReferral;
use App\Models\LegalPartnerProfile;
use App\Models\MallMirrorBusiness;
use App\Models\MallMirrorProduct;
use App\Models\MallMirrorService;
use App\Models\MallPublicationConsent;
use App\Models\MarketplaceCatalogItem;
use App\Models\ModerationCase;
use App\Models\ModerationFlag;
use App\Models\Organization;
use App\Models\Package;
use App\Models\PartnerCatalogShare;
use App\Models\PartnerStorefront;
use App\Models\ReputationSnapshot;
use App\Models\Review;
use App\Models\Theme;
use App\Models\TrustBadge;
use App\Models\TrustBadgeAward;
use App\Models\User;
use App\Models\WorkNetworkProfile;
use Illuminate\Database\Seeder;

class V4DemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->where('slug', 'kabeeri-demo-org')->first()
            ?? Organization::query()->first();
        $admin = User::query()->where('email', 'admin@kabeeri.local')->first()
            ?? User::query()->first();

        if (! $organization || ! $admin) {
            return;
        }

        $company = Company::query()->where('organization_id', $organization->id)->first();
        $site = $organization->sites()->first();
        $businessProfile = BusinessProfile::query()->where('organization_id', $organization->id)->first();

        $consent = MallPublicationConsent::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'consent_type' => 'mall_publication',
                'subject_type' => BusinessProfile::class,
                'subject_id' => $businessProfile?->id,
            ],
            [
                'company_id' => $company?->id,
                'site_id' => $site?->id,
                'status' => 'granted',
                'channels' => ['business_directory', 'products', 'services'],
                'granted_by' => $admin->id,
                'granted_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        $business = MallMirrorBusiness::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v4-demo-business'],
            [
                'company_id' => $company?->id,
                'site_id' => $site?->id,
                'business_profile_id' => $businessProfile?->id,
                'mall_publication_consent_id' => $consent->id,
                'display_name' => 'V4 Demo Business',
                'description' => 'Published V4 demo business for the public Mall.',
                'public_contacts' => ['email' => 'v4-business@example.test'],
                'mirror_status' => 'published',
                'published_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        MallMirrorProduct::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v4-demo-product'],
            [
                'company_id' => $company?->id,
                'site_id' => $site?->id,
                'mall_publication_consent_id' => $consent->id,
                'product_name' => 'V4 Demo Product',
                'description' => 'Published V4 demo product.',
                'price' => 99,
                'currency' => 'USD',
                'mirror_status' => 'published',
                'published_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        MallMirrorService::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v4-demo-service'],
            [
                'company_id' => $company?->id,
                'site_id' => $site?->id,
                'mall_publication_consent_id' => $consent->id,
                'service_name' => 'V4 Demo Service',
                'description' => 'Published V4 demo service.',
                'service_category' => 'implementation',
                'hourly_rate' => 50,
                'currency' => 'USD',
                'mirror_status' => 'published',
                'published_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        $case = ModerationCase::query()->updateOrCreate(
            ['case_number' => 'MOD-V4-DEMO-001'],
            [
                'organization_id' => $organization->id,
                'site_id' => $site?->id,
                'subject_type' => $business->getMorphClass(),
                'subject_id' => $business->id,
                'case_type' => 'listing_quality',
                'reason' => 'demo_review',
                'priority' => 'normal',
                'status' => 'open',
                'opened_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        ModerationFlag::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'subject_type' => $business->getMorphClass(),
                'subject_id' => $business->id,
                'flag_type' => 'demo_report',
            ],
            [
                'site_id' => $site?->id,
                'moderation_case_id' => $case->id,
                'reason' => 'demo',
                'severity' => 'normal',
                'status' => 'new',
                'message' => 'Demo moderation flag.',
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        Review::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'reviewable_type' => $business->getMorphClass(),
                'reviewable_id' => $business->id,
                'title' => 'V4 Demo Review',
            ],
            [
                'site_id' => $site?->id,
                'reviewer_user_id' => $admin->id,
                'rating' => 5,
                'body' => 'Seeded public trust demo review.',
                'source' => 'demo',
                'status' => 'published',
                'submitted_at' => now(),
                'published_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        ReputationSnapshot::query()->updateOrCreate(
            ['subject_type' => $business->getMorphClass(), 'subject_id' => $business->id],
            [
                'organization_id' => $organization->id,
                'site_id' => $site?->id,
                'review_count' => 1,
                'published_review_count' => 1,
                'average_rating' => 5,
                'open_moderation_cases_count' => 1,
                'trust_score' => 91,
                'calculated_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        $package = Package::query()->updateOrCreate(
            ['key' => 'kabeeri.v4-demo-package'],
            [
                'name' => 'Kabeeri V4 Demo Package',
                'slug' => 'kabeeri-v4-demo-package',
                'package_type' => 'module',
                'publisher_type' => 'official',
                'status' => 'active',
                'category' => 'v4',
                'permissions' => ['marketplace.view'],
                'dependencies' => [],
                'compatibility' => ['v4' => true],
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        MarketplaceCatalogItem::query()->updateOrCreate(
            ['catalogable_type' => $package->getMorphClass(), 'catalogable_id' => $package->id],
            [
                'item_kind' => 'package',
                'listing_status' => 'active',
                'visibility' => 'internal',
                'governance_status' => 'approved',
                'is_featured' => true,
                'approved_by_user_id' => $admin->id,
                'approved_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        if ($theme = Theme::query()->where('slug', 'kabeeri-starter')->first()) {
            MarketplaceCatalogItem::query()->updateOrCreate(
                ['catalogable_type' => $theme->getMorphClass(), 'catalogable_id' => $theme->id],
                [
                    'item_kind' => 'theme',
                    'listing_status' => 'active',
                    'visibility' => 'internal',
                    'governance_status' => 'approved',
                    'is_featured' => true,
                    'approved_by_user_id' => $admin->id,
                    'approved_at' => now(),
                    'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
                ],
            );
        }

        $agency = AgencyPartnerProfile::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v4-demo-agency'],
            [
                'company_id' => $company?->id,
                'display_name' => 'V4 Demo Agency',
                'agency_type' => 'implementation_partner',
                'status' => 'active',
                'accreditation_status' => 'accredited',
                'accreditation_level' => 'standard',
                'service_categories' => ['implementation', 'migration'],
                'regions' => ['EG'],
                'languages' => ['ar', 'en'],
                'submitted_at' => now(),
                'accredited_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        AgencyDashboardSnapshot::query()->updateOrCreate(
            ['agency_partner_profile_id' => $agency->id, 'period_start' => now()->startOfMonth()->toDateString()],
            [
                'organization_id' => $organization->id,
                'period_end' => now()->endOfMonth()->toDateString(),
                'metrics' => ['status' => 'active', 'accreditation_level' => 'standard'],
                'alerts' => [],
                'generated_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        LegalPartnerProfile::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v4-demo-legal-partner'],
            [
                'company_id' => $company?->id,
                'display_name' => 'V4 Demo Legal Partner',
                'partner_type' => 'legal_consultant',
                'practice_areas' => ['contracts'],
                'jurisdictions' => ['EG'],
                'languages' => ['ar', 'en'],
                'verification_status' => 'verified',
                'network_status' => 'active',
                'verified_at' => now(),
                'activated_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        CreatorProfile::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v4-demo-creator'],
            [
                'user_id' => $admin->id,
                'display_name' => 'V4 Demo Creator',
                'profile_type' => 'creator',
                'status' => 'active',
                'verification_status' => 'not_submitted',
                'specialties' => ['themes', 'packages'],
                'links' => [],
                'submitted_at' => now(),
                'approved_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        $workProfile = WorkNetworkProfile::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v4-demo-work-profile'],
            [
                'user_id' => $admin->id,
                'display_name' => 'V4 Demo Work Profile',
                'headline' => 'KABEERI V4 Specialist',
                'skills' => ['mall', 'migration'],
                'visibility' => 'public',
                'status' => 'published',
                'availability_status' => 'available',
                'published_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        $trustBadge = TrustBadge::query()->updateOrCreate(
            ['key' => 'v4-demo-trusted'],
            ['name' => 'V4 Demo Trusted', 'badge_type' => 'verification', 'status' => 'active', 'metadata' => ['seeded' => true]],
        );

        TrustBadgeAward::query()->updateOrCreate(
            ['trust_badge_id' => $trustBadge->id, 'subject_type' => $business->getMorphClass(), 'subject_id' => $business->id],
            [
                'organization_id' => $organization->id,
                'site_id' => $site?->id,
                'awarded_by_user_id' => $admin->id,
                'status' => 'active',
                'awarded_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        $academyBadge = AcademyBadge::query()->updateOrCreate(
            ['key' => 'v4-demo-academy-mall-basic'],
            ['name' => 'V4 Mall Basic', 'badge_type' => 'skill', 'status' => 'active', 'metadata' => ['seeded' => true]],
        );

        AcademyBadgeAward::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'academy_badge_id' => $academyBadge->id, 'work_network_profile_id' => $workProfile->id],
            [
                'user_id' => $admin->id,
                'awarded_by_user_id' => $admin->id,
                'status' => 'active',
                'awarded_at' => now(),
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        GrowthReferral::query()->updateOrCreate(
            ['code' => 'REF-V4-DEMO'],
            [
                'organization_id' => $organization->id,
                'referrer_user_id' => $admin->id,
                'referred_email' => 'referral@example.test',
                'source' => 'demo',
                'status' => 'pending',
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        $storefront = PartnerStorefront::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v4-demo-partner-storefront'],
            [
                'agency_partner_profile_id' => $agency->id,
                'name' => 'V4 Demo Partner Storefront',
                'storefront_type' => 'partner_catalog',
                'status' => 'draft',
                'visibility' => 'private',
                'settings' => ['mode' => 'draft'],
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );

        PartnerCatalogShare::query()->updateOrCreate(
            [
                'partner_storefront_id' => $storefront->id,
                'catalogable_type' => $package->getMorphClass(),
                'catalogable_id' => $package->id,
            ],
            [
                'organization_id' => $organization->id,
                'share_type' => 'catalog_item',
                'status' => 'draft',
                'visibility' => 'private',
                'metadata' => ['seeded' => true, 'source' => 'v4_demo'],
            ],
        );
    }
}
