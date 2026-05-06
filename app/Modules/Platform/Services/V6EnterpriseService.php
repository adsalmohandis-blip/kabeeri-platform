<?php

namespace App\Modules\Platform\Services;

use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class V6EnterpriseService
{
    /**
     * Create one safe, auditable foundation record for every V6 domain.
     *
     * @return array<string, int>
     */
    public function seedFoundationRecords(Organization $organization): array
    {
        $ids = [];

        $ids['identity_provider'] = $this->insert('enterprise_identity_providers', [
            'organization_id' => $organization->id,
            'provider_type' => 'saml',
            'name' => 'V6 Demo SSO',
            'status' => 'draft',
            'metadata_reference' => 'vault://sso/metadata',
            'certificate_reference' => 'vault://sso/certificate',
        ]);
        $ids['mfa_policy'] = $this->insert('enterprise_mfa_policies', [
            'organization_id' => $organization->id,
            'name' => 'V6 MFA Policy',
            'status' => 'draft',
            'enforcement_level' => 'optional',
            'allowed_methods' => ['totp', 'webauthn'],
        ]);
        $ids['scim_directory'] = $this->insert('enterprise_scim_directories', [
            'organization_id' => $organization->id,
            'name' => 'V6 SCIM Directory',
            'status' => 'draft',
            'token_reference' => 'vault://scim/token',
        ]);

        $ids['security_stream'] = $this->insert('security_export_streams', [
            'organization_id' => $organization->id,
            'destination_type' => 'siem',
            'name' => 'V6 SIEM Stream',
            'status' => 'draft',
            'endpoint_reference' => 'vault://siem/endpoint',
        ]);
        $ids['security_event'] = $this->insert('security_export_events', [
            'organization_id' => $organization->id,
            'security_export_stream_id' => $ids['security_stream'],
            'event_type' => 'audit.demo',
            'status' => 'queued',
            'payload' => ['source' => 'v6_demo'],
        ], false);

        $ids['publisher'] = $this->insert('developer_publishers', [
            'organization_id' => $organization->id,
            'display_name' => 'V6 Demo Publisher',
            'slug' => 'v6-demo-publisher-'.$organization->id,
            'status' => 'pending_review',
        ]);
        $ids['marketplace_listing'] = $this->insert('developer_marketplace_listings', [
            'developer_publisher_id' => $ids['publisher'],
            'listing_type' => 'package',
            'title' => 'V6 Demo Listing',
            'slug' => 'v6-demo-listing-'.$organization->id,
            'status' => 'draft',
            'visibility' => 'private',
        ]);
        $ids['package_release_governance'] = $this->insert('package_release_governance_records', [
            'release_version' => '6.0.0',
            'status' => 'pending_review',
            'signature_reference' => 'vault://package-signing/pending',
            'checks' => ['manifest' => 'pending', 'signature' => 'pending'],
        ]);
        $ids['developer_certification'] = $this->insert('developer_certifications', [
            'certification_key' => 'v6-developer-foundation',
            'status' => 'draft',
        ]);
        $ids['developer_docs'] = $this->insert('developer_docs_pages', [
            'slug' => 'v6-enterprise-api-'.$organization->id,
            'title' => 'V6 Enterprise API',
            'status' => 'draft',
            'version' => 'v6',
            'body' => 'Draft developer documentation placeholder.',
        ]);

        $ids['connector_sdk'] = $this->insert('connector_sdk_definitions', [
            'key' => 'v6-demo-sdk-'.$organization->id,
            'name' => 'V6 Demo SDK',
            'status' => 'draft',
            'schema' => ['auth' => 'vault_reference'],
        ]);
        $ids['universal_sync'] = $this->insert('universal_sync_profiles', [
            'organization_id' => $organization->id,
            'connector_sdk_definition_id' => $ids['connector_sdk'],
            'name' => 'V6 Universal Sync Preview',
            'status' => 'preview',
            'rules' => ['mode' => 'preview'],
        ]);

        foreach ([
            'mall_advanced_sections' => 'Universal Mall Section',
            'mall_lms_programs' => 'Mall LMS Program',
            'mall_travel_experiences' => 'Mall Travel Experience',
            'mall_rfq_requests' => 'Mall RFQ Request',
            'mall_deal_offers' => 'Mall Deal Offer',
            'mall_property_assets' => 'Mall Property Asset',
        ] as $table => $title) {
            $ids[$table] = $this->insert($table, [
                'organization_id' => $organization->id,
                'title' => 'V6 '.$title,
                'slug' => Str::slug('v6-'.$title.'-'.$organization->id),
                'status' => 'draft',
                'visibility' => 'private',
                'content' => ['source' => 'v6_demo'],
            ]);
        }

        $ids['data_pipeline'] = $this->insert('data_ingestion_pipelines', [
            'organization_id' => $organization->id,
            'name' => 'V6 Data Pipeline',
            'source_type' => 'database',
            'status' => 'draft',
            'schedule' => ['mode' => 'manual'],
        ]);
        $ids['data_mart'] = $this->insert('data_marts', [
            'organization_id' => $organization->id,
            'name' => 'V6 Executive Mart',
            'mart_key' => 'v6_executive_'.$organization->id,
            'status' => 'draft',
        ]);
        $ids['metric'] = $this->insert('metric_definitions', [
            'organization_id' => $organization->id,
            'key' => 'v6.revenue.pipeline.'.$organization->id,
            'name' => 'V6 Pipeline Revenue',
            'status' => 'draft',
            'aggregation' => 'sum',
        ]);
        $ids['bi_dashboard'] = $this->insert('enterprise_bi_dashboards', [
            'organization_id' => $organization->id,
            'name' => 'V6 BI Dashboard',
            'slug' => 'v6-bi-'.$organization->id,
            'status' => 'draft',
            'layout' => ['widgets' => ['kpi', 'risk', 'sync']],
        ]);
        $ids['dictionary_term'] = $this->insert('data_dictionary_terms', [
            'organization_id' => $organization->id,
            'term' => 'Customer',
            'domain' => 'core',
            'definition' => 'V6 glossary placeholder.',
            'status' => 'draft',
        ]);
        $ids['lineage'] = $this->insert('data_lineage_links', [
            'source_name' => 'crm.contacts',
            'target_name' => 'mart.customers',
            'status' => 'draft',
        ]);

        foreach ([
            'grc_policies' => 'V6 GRC Policy',
            'grc_obligations' => 'V6 Compliance Obligation',
            'grc_risks' => 'V6 Risk',
            'grc_controls' => 'V6 Control',
            'audit_engagements' => 'V6 Audit Engagement',
            'audit_evidence_items' => 'V6 Evidence',
            'audit_remediations' => 'V6 Remediation',
        ] as $table => $title) {
            $ids[$table] = $this->insert($table, [
                'organization_id' => $organization->id,
                'title' => $title,
                'status' => 'draft',
                'risk_level' => 'medium',
            ]);
        }

        $ids['enterprise_application'] = $this->insert('enterprise_applications', [
            'organization_id' => $organization->id,
            'name' => 'V6 Portfolio App',
            'portfolio_status' => 'assess',
        ]);
        $ids['integration_map'] = $this->insert('enterprise_integration_maps', [
            'organization_id' => $organization->id,
            'name' => 'V6 Integration Map',
            'status' => 'draft',
            'nodes' => ['crm', 'erp'],
            'edges' => [['from' => 'crm', 'to' => 'erp']],
        ]);
        $ids['impact_analysis'] = $this->insert('change_impact_analyses', [
            'organization_id' => $organization->id,
            'title' => 'V6 Change Impact',
            'status' => 'draft',
            'impact_summary' => ['risk' => 'medium'],
        ]);

        foreach ([
            'industry_solution_templates' => 'Industry Solution',
            'esg_ehs_records' => 'ESG EHS Record',
            'plm_items' => 'PLM Item',
            'manufacturing_assets' => 'Manufacturing Asset',
            'retail_omnichannel_channels' => 'Retail Channel',
            'hcm_payroll_profiles' => 'HCM Payroll Profile',
            'pmo_service_projects' => 'PMO Project',
            'contact_center_cases' => 'Contact Center Case',
            'legal_clm_playbooks' => 'Legal CLM Playbook',
        ] as $table => $name) {
            $ids[$table] = $this->insert($table, [
                'organization_id' => $organization->id,
                'name' => 'V6 '.$name,
                'status' => 'draft',
            ]);
        }

        $ids['ai_agent'] = $this->insert('ai_cobuilder_agents', [
            'organization_id' => $organization->id,
            'name' => 'V6 Guarded Agent',
            'agent_type' => 'planner',
            'status' => 'draft',
            'guardrails' => ['mode' => 'plan_preview_apply_rollback'],
        ]);
        $ids['ai_skill'] = $this->insert('ai_skill_marketplace_listings', [
            'developer_publisher_id' => $ids['publisher'],
            'name' => 'V6 AI Skill',
            'status' => 'pending_review',
            'manifest' => ['safe_execution' => false],
        ]);

        foreach ([
            'work_network_levels' => 'Work Network Level',
            'agency_partner_operations' => 'Agency Operation',
            'academy_assessments' => 'Academy Assessment',
            'marketplace_revenue_share_rules' => 'Revenue Share Rule',
            'marketplace_payout_batches' => 'Marketplace Payout Batch',
        ] as $table => $name) {
            $ids[$table] = $this->insert($table, [
                'organization_id' => $organization->id,
                'name' => 'V6 '.$name,
                'status' => 'draft',
                'amount' => 0,
            ]);
        }

        $ids['api_gateway_route'] = $this->insert('api_gateway_routes', [
            'route_key' => 'v6.demo.'.$organization->id,
            'status' => 'draft',
            'rate_limit_bucket' => 'enterprise-default',
        ]);
        $ids['api_version'] = $this->insert('public_api_versions', [
            'version' => 'v6-'.$organization->id,
            'status' => 'draft',
            'docs_path' => '/docs/api/v6',
        ]);
        $ids['retention_policy'] = $this->insert('data_privacy_retention_policies', [
            'organization_id' => $organization->id,
            'name' => 'V6 Retention Policy',
            'status' => 'draft',
            'retention_days' => 365,
        ]);
        $ids['queue_profile'] = $this->insert('performance_queue_profiles', [
            'name' => 'V6 Default Queue',
            'queue_name' => 'default',
            'status' => 'draft',
            'max_attempts' => 3,
        ]);

        return $ids;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function insert(string $table, array $attributes, bool $withUlid = true): int
    {
        $payload = $attributes;

        if ($withUlid && ! array_key_exists('ulid', $payload)) {
            $payload['ulid'] = (string) Str::ulid();
        }

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = json_encode($value);
            }
        }

        if (Schema::hasColumn($table, 'metadata')) {
            $payload['metadata'] = $payload['metadata'] ?? json_encode(['source' => 'v6_enterprise_service']);
        }
        $payload['created_at'] = now();
        $payload['updated_at'] = now();

        return (int) DB::table($table)->insertGetId($payload);
    }
}
