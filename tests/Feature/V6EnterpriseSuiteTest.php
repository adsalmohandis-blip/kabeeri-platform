<?php

namespace Tests\Feature;

use App\Filament\Resources\DataIngestionPipelines\DataIngestionPipelineResource;
use App\Filament\Resources\DeveloperMarketplaceListings\DeveloperMarketplaceListingResource;
use App\Filament\Resources\GrcRisks\GrcRiskResource;
use App\Models\DataIngestionPipeline;
use App\Models\DeveloperMarketplaceListing;
use App\Models\GrcRisk;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use App\Modules\Platform\Services\V6EnterpriseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class V6EnterpriseSuiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_v6_enterprise_tables_exist_for_every_tracker_domain(): void
    {
        foreach ($this->v6Tables() as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing V6 table [{$table}].");
        }
    }

    public function test_v6_enterprise_service_creates_safe_foundation_records(): void
    {
        $organization = Organization::factory()->create();

        $ids = app(V6EnterpriseService::class)->seedFoundationRecords($organization);

        foreach ([
            'identity_provider' => 'enterprise_identity_providers',
            'mfa_policy' => 'enterprise_mfa_policies',
            'scim_directory' => 'enterprise_scim_directories',
            'security_stream' => 'security_export_streams',
            'publisher' => 'developer_publishers',
            'marketplace_listing' => 'developer_marketplace_listings',
            'connector_sdk' => 'connector_sdk_definitions',
            'universal_sync' => 'universal_sync_profiles',
            'data_pipeline' => 'data_ingestion_pipelines',
            'data_mart' => 'data_marts',
            'metric' => 'metric_definitions',
            'bi_dashboard' => 'enterprise_bi_dashboards',
            'grc_risks' => 'grc_risks',
            'enterprise_application' => 'enterprise_applications',
            'industry_solution_templates' => 'industry_solution_templates',
            'ai_agent' => 'ai_cobuilder_agents',
            'api_gateway_route' => 'api_gateway_routes',
            'retention_policy' => 'data_privacy_retention_policies',
            'queue_profile' => 'performance_queue_profiles',
        ] as $key => $table) {
            $this->assertDatabaseHas($table, ['id' => $ids[$key]]);
        }

        $this->assertDatabaseHas('enterprise_identity_providers', [
            'id' => $ids['identity_provider'],
            'certificate_reference' => 'vault://sso/certificate',
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('security_export_events', [
            'id' => $ids['security_event'],
            'status' => 'queued',
        ]);
        $this->assertDatabaseHas('developer_marketplace_listings', [
            'id' => $ids['marketplace_listing'],
            'visibility' => 'private',
        ]);
        $this->assertDatabaseHas('universal_sync_profiles', [
            'id' => $ids['universal_sync'],
            'status' => 'preview',
        ]);
        $this->assertDatabaseHas('marketplace_payout_batches', [
            'id' => $ids['marketplace_payout_batches'],
            'status' => 'draft',
        ]);
    }

    public function test_v6_admin_resources_are_registered_and_tenant_scoped(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        OrganizationMembership::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => 'active',
        ]);

        $ownedPipeline = DataIngestionPipeline::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Owned Pipeline',
            'source_type' => 'database',
        ]);
        $ownedRisk = GrcRisk::query()->create([
            'organization_id' => $organization->id,
            'title' => 'Owned Risk',
            'risk_level' => 'medium',
        ]);
        $listing = DeveloperMarketplaceListing::query()->create([
            'title' => 'Public Developer Draft',
            'slug' => 'public-developer-draft',
        ]);

        DataIngestionPipeline::query()->create([
            'organization_id' => Organization::factory()->create()->id,
            'name' => 'Other Pipeline',
        ]);
        GrcRisk::query()->create([
            'organization_id' => Organization::factory()->create()->id,
            'title' => 'Other Risk',
        ]);

        $this->actingAs($user);

        $this->assertArrayHasKey('index', DataIngestionPipelineResource::getPages());
        $this->assertArrayHasKey('index', GrcRiskResource::getPages());
        $this->assertArrayHasKey('index', DeveloperMarketplaceListingResource::getPages());
        $this->assertSame([$ownedPipeline->id], DataIngestionPipelineResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$ownedRisk->id], GrcRiskResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$listing->id], DeveloperMarketplaceListingResource::getEloquentQuery()->pluck('id')->all());
    }

    public function test_v6_security_boundaries_do_not_store_raw_secrets_or_execute_payouts(): void
    {
        $organization = Organization::factory()->create();
        app(V6EnterpriseService::class)->seedFoundationRecords($organization);

        $this->assertFalse(Schema::hasColumn('enterprise_identity_providers', 'raw_certificate'));
        $this->assertFalse(Schema::hasColumn('enterprise_scim_directories', 'raw_token'));
        $this->assertFalse(Schema::hasColumn('security_export_streams', 'raw_secret'));
        $this->assertFalse(Schema::hasColumn('marketplace_payout_batches', 'paid_at'));
        $this->assertSame(1, DB::table('marketplace_payout_batches')->where('status', 'draft')->count());
    }

    /**
     * @return list<string>
     */
    private function v6Tables(): array
    {
        return [
            'enterprise_identity_providers',
            'enterprise_mfa_policies',
            'enterprise_scim_directories',
            'security_export_streams',
            'security_export_events',
            'developer_publishers',
            'developer_marketplace_listings',
            'package_release_governance_records',
            'developer_certifications',
            'developer_docs_pages',
            'connector_sdk_definitions',
            'universal_sync_profiles',
            'mall_advanced_sections',
            'mall_lms_programs',
            'mall_travel_experiences',
            'mall_rfq_requests',
            'mall_deal_offers',
            'mall_property_assets',
            'data_ingestion_pipelines',
            'data_marts',
            'metric_definitions',
            'enterprise_bi_dashboards',
            'data_dictionary_terms',
            'data_lineage_links',
            'grc_policies',
            'grc_obligations',
            'grc_risks',
            'grc_controls',
            'audit_engagements',
            'audit_evidence_items',
            'audit_remediations',
            'enterprise_applications',
            'enterprise_integration_maps',
            'change_impact_analyses',
            'industry_solution_templates',
            'esg_ehs_records',
            'plm_items',
            'manufacturing_assets',
            'retail_omnichannel_channels',
            'hcm_payroll_profiles',
            'pmo_service_projects',
            'contact_center_cases',
            'legal_clm_playbooks',
            'ai_cobuilder_agents',
            'ai_skill_marketplace_listings',
            'work_network_levels',
            'agency_partner_operations',
            'academy_assessments',
            'marketplace_revenue_share_rules',
            'marketplace_payout_batches',
            'api_gateway_routes',
            'public_api_versions',
            'data_privacy_retention_policies',
            'performance_queue_profiles',
        ];
    }
}
