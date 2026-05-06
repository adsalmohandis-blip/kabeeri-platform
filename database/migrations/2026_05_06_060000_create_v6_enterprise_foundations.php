<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $json = fn (Blueprint $table, string $name = 'metadata') => $table->json($name)->nullable();

        Schema::create('enterprise_identity_providers', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('provider_type', 40)->default('saml');
            $table->string('name');
            $table->string('status', 40)->default('draft');
            $table->string('metadata_reference')->nullable();
            $table->string('certificate_reference')->nullable();
            $json($table, 'settings');
            $json($table);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('enterprise_mfa_policies', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('status', 40)->default('draft');
            $table->string('enforcement_level', 40)->default('optional');
            $json($table, 'allowed_methods');
            $json($table);
            $table->timestamps();
        });

        Schema::create('enterprise_scim_directories', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('status', 40)->default('draft');
            $table->string('token_reference')->nullable();
            $json($table, 'mapping');
            $json($table);
            $table->timestamps();
        });

        Schema::create('security_export_streams', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('destination_type', 60)->default('siem');
            $table->string('name');
            $table->string('status', 40)->default('draft');
            $table->string('endpoint_reference')->nullable();
            $json($table, 'event_filters');
            $json($table);
            $table->timestamps();
        });

        Schema::create('security_export_events', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('security_export_stream_id')->nullable()->constrained('security_export_streams')->nullOnDelete();
            $table->string('event_type', 120);
            $table->string('status', 40)->default('queued');
            $json($table, 'payload');
            $table->timestamps();
        });

        Schema::create('developer_publishers', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('display_name');
            $table->string('slug')->unique();
            $table->string('status', 40)->default('pending_review');
            $json($table);
            $table->timestamps();
        });

        Schema::create('developer_marketplace_listings', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('developer_publisher_id')->nullable()->constrained('developer_publishers')->nullOnDelete();
            $table->nullableMorphs('listable');
            $table->string('listing_type', 60)->default('package');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('status', 40)->default('draft');
            $table->string('visibility', 40)->default('private');
            $json($table, 'review_summary');
            $json($table);
            $table->timestamps();
        });

        Schema::create('package_release_governance_records', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->string('release_version');
            $table->string('status', 40)->default('pending_review');
            $table->string('signature_reference')->nullable();
            $json($table, 'checks');
            $json($table);
            $table->timestamps();
        });

        Schema::create('developer_certifications', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('certification_key');
            $table->string('status', 40)->default('draft');
            $table->timestamp('awarded_at')->nullable();
            $json($table);
            $table->timestamps();
        });

        Schema::create('developer_docs_pages', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('status', 40)->default('draft');
            $table->string('version', 40)->default('v1');
            $table->longText('body')->nullable();
            $json($table);
            $table->timestamps();
        });

        Schema::create('connector_sdk_definitions', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('status', 40)->default('draft');
            $json($table, 'schema');
            $json($table);
            $table->timestamps();
        });

        Schema::create('universal_sync_profiles', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('connector_sdk_definition_id')->nullable()->constrained('connector_sdk_definitions')->nullOnDelete();
            $table->string('name');
            $table->string('status', 40)->default('preview');
            $json($table, 'rules');
            $json($table);
            $table->timestamps();
        });

        foreach ([
            'mall_advanced_sections',
            'mall_lms_programs',
            'mall_travel_experiences',
            'mall_rfq_requests',
            'mall_deal_offers',
            'mall_property_assets',
        ] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) use ($json): void {
                $table->id();
                $table->ulid('ulid')->unique();
                $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('status', 40)->default('draft');
                $table->string('visibility', 40)->default('private');
                $json($table, 'content');
                $json($table);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::create('data_ingestion_pipelines', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('source_type', 80)->default('database');
            $table->string('status', 40)->default('draft');
            $json($table, 'schedule');
            $json($table);
            $table->timestamps();
        });

        Schema::create('data_marts', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('mart_key');
            $table->string('status', 40)->default('draft');
            $json($table, 'dimensions');
            $json($table);
            $table->timestamps();
        });

        Schema::create('metric_definitions', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->string('status', 40)->default('draft');
            $table->string('aggregation', 40)->default('count');
            $json($table, 'formula');
            $json($table);
            $table->timestamps();
        });

        Schema::create('enterprise_bi_dashboards', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('status', 40)->default('draft');
            $json($table, 'layout');
            $json($table);
            $table->timestamps();
        });

        Schema::create('data_dictionary_terms', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('term');
            $table->string('domain', 80)->default('general');
            $table->text('definition')->nullable();
            $table->string('status', 40)->default('draft');
            $json($table);
            $table->timestamps();
        });

        Schema::create('data_lineage_links', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('source_name');
            $table->string('target_name');
            $table->string('status', 40)->default('draft');
            $json($table);
            $table->timestamps();
        });

        foreach ([
            'grc_policies',
            'grc_obligations',
            'grc_risks',
            'grc_controls',
            'audit_engagements',
            'audit_evidence_items',
            'audit_remediations',
        ] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) use ($json): void {
                $table->id();
                $table->ulid('ulid')->unique();
                $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
                $table->string('title');
                $table->string('status', 40)->default('draft');
                $table->string('risk_level', 40)->nullable();
                $json($table);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::create('enterprise_applications', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('portfolio_status', 40)->default('assess');
            $json($table);
            $table->timestamps();
        });

        Schema::create('enterprise_integration_maps', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('status', 40)->default('draft');
            $json($table, 'nodes');
            $json($table, 'edges');
            $json($table);
            $table->timestamps();
        });

        Schema::create('change_impact_analyses', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('title');
            $table->string('status', 40)->default('draft');
            $json($table, 'impact_summary');
            $json($table);
            $table->timestamps();
        });

        foreach ([
            'industry_solution_templates',
            'esg_ehs_records',
            'plm_items',
            'manufacturing_assets',
            'retail_omnichannel_channels',
            'hcm_payroll_profiles',
            'pmo_service_projects',
            'contact_center_cases',
            'legal_clm_playbooks',
        ] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) use ($json): void {
                $table->id();
                $table->ulid('ulid')->unique();
                $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
                $table->string('name');
                $table->string('status', 40)->default('draft');
                $json($table);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::create('ai_cobuilder_agents', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('agent_type', 80)->default('planner');
            $table->string('status', 40)->default('draft');
            $json($table, 'guardrails');
            $json($table);
            $table->timestamps();
        });

        Schema::create('ai_skill_marketplace_listings', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('developer_publisher_id')->nullable()->constrained('developer_publishers')->nullOnDelete();
            $table->string('name');
            $table->string('status', 40)->default('pending_review');
            $json($table, 'manifest');
            $json($table);
            $table->timestamps();
        });

        foreach ([
            'work_network_levels',
            'agency_partner_operations',
            'academy_assessments',
            'marketplace_revenue_share_rules',
            'marketplace_payout_batches',
        ] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) use ($json): void {
                $table->id();
                $table->ulid('ulid')->unique();
                $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
                $table->string('name');
                $table->string('status', 40)->default('draft');
                $table->decimal('amount', 14, 2)->default(0);
                $json($table);
                $table->timestamps();
            });
        }

        Schema::create('api_gateway_routes', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('route_key')->unique();
            $table->string('status', 40)->default('draft');
            $table->string('rate_limit_bucket')->nullable();
            $json($table);
            $table->timestamps();
        });

        Schema::create('public_api_versions', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('version')->unique();
            $table->string('status', 40)->default('draft');
            $table->string('docs_path')->nullable();
            $json($table);
            $table->timestamps();
        });

        Schema::create('data_privacy_retention_policies', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('status', 40)->default('draft');
            $table->unsignedInteger('retention_days')->default(365);
            $json($table);
            $table->timestamps();
        });

        Schema::create('performance_queue_profiles', function (Blueprint $table) use ($json): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('name');
            $table->string('queue_name')->default('default');
            $table->string('status', 40)->default('draft');
            $table->unsignedInteger('max_attempts')->default(3);
            $json($table);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'performance_queue_profiles',
            'data_privacy_retention_policies',
            'public_api_versions',
            'api_gateway_routes',
            'marketplace_payout_batches',
            'marketplace_revenue_share_rules',
            'academy_assessments',
            'agency_partner_operations',
            'work_network_levels',
            'ai_skill_marketplace_listings',
            'ai_cobuilder_agents',
            'legal_clm_playbooks',
            'contact_center_cases',
            'pmo_service_projects',
            'hcm_payroll_profiles',
            'retail_omnichannel_channels',
            'manufacturing_assets',
            'plm_items',
            'esg_ehs_records',
            'industry_solution_templates',
            'change_impact_analyses',
            'enterprise_integration_maps',
            'enterprise_applications',
            'audit_remediations',
            'audit_evidence_items',
            'audit_engagements',
            'grc_controls',
            'grc_risks',
            'grc_obligations',
            'grc_policies',
            'data_lineage_links',
            'data_dictionary_terms',
            'enterprise_bi_dashboards',
            'metric_definitions',
            'data_marts',
            'data_ingestion_pipelines',
            'mall_property_assets',
            'mall_deal_offers',
            'mall_rfq_requests',
            'mall_travel_experiences',
            'mall_lms_programs',
            'mall_advanced_sections',
            'universal_sync_profiles',
            'connector_sdk_definitions',
            'developer_docs_pages',
            'developer_certifications',
            'package_release_governance_records',
            'developer_marketplace_listings',
            'developer_publishers',
            'security_export_events',
            'security_export_streams',
            'enterprise_scim_directories',
            'enterprise_mfa_policies',
            'enterprise_identity_providers',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
