<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_documents', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('erp_pro_opportunity_id')->nullable()->constrained('erp_pro_opportunities')->nullOnDelete();
            $table->string('document_type', 60)->default('proposal');
            $table->string('document_number');
            $table->string('title');
            $table->string('status', 40)->default('draft');
            $table->string('currency_code', 3)->default('EGP');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamp('issued_at')->nullable();
            $table->json('terms')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'document_number']);
            $table->index(['organization_id', 'document_type', 'status']);
        });

        Schema::create('sales_document_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_document_id')->constrained('sales_documents')->cascadeOnDelete();
            $table->nullableMorphs('lineable');
            $table->string('name');
            $table->decimal('quantity', 14, 2)->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_links', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->nullableMorphs('linkable');
            $table->string('link_type', 60)->default('source');
            $table->decimal('amount', 14, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_reservations', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->nullableMorphs('reservable');
            $table->decimal('quantity', 14, 2)->default(0);
            $table->string('status', 40)->default('reserved');
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rfqs', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('rfq_number');
            $table->string('title');
            $table->string('status', 40)->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('response_due_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'rfq_number']);
        });

        Schema::create('rfq_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->string('name');
            $table->decimal('quantity', 14, 2)->default(1);
            $table->decimal('target_unit_price', 14, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_posting_batches', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('batch_number');
            $table->string('posting_source', 80);
            $table->string('status', 40)->default('draft');
            $table->decimal('debit_total', 14, 2)->default(0);
            $table->decimal('credit_total', 14, 2)->default(0);
            $table->timestamp('posted_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'batch_number']);
        });

        Schema::create('accounting_posting_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('accounting_posting_batch_id')->constrained('accounting_posting_batches')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->nullableMorphs('source');
            $table->string('description')->nullable();
            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('credit', 14, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_terminals', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('status', 40)->default('inactive');
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'code']);
        });

        Schema::create('pos_sessions', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('pos_terminal_id')->constrained('pos_terminals')->cascadeOnDelete();
            $table->foreignId('opened_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_number');
            $table->string('status', 40)->default('open');
            $table->decimal('opening_cash', 14, 2)->default(0);
            $table->decimal('closing_cash', 14, 2)->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_sales', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('pos_session_id')->nullable()->constrained('pos_sessions')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('receipt_number');
            $table->string('status', 40)->default('draft');
            $table->string('currency_code', 3)->default('EGP');
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->json('payment_summary')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('advanced_approval_policies', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('applies_to', 120);
            $table->string('status', 40)->default('draft');
            $table->json('conditions')->nullable();
            $table->json('steps')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('integration_oauth_states', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_connector_id')->constrained('integration_connectors')->cascadeOnDelete();
            $table->string('state')->unique();
            $table->string('status', 40)->default('pending');
            $table->string('redirect_uri')->nullable();
            $table->timestamp('expires_at');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_field_mappings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_connector_id')->constrained('integration_connectors')->cascadeOnDelete();
            $table->string('local_model', 160);
            $table->string('external_object_type', 120);
            $table->string('local_field');
            $table->string('external_field');
            $table->string('direction', 40)->default('import');
            $table->string('transform', 80)->nullable();
            $table->boolean('is_required')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_value_mappings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_connector_id')->constrained('integration_connectors')->cascadeOnDelete();
            $table->string('mapping_group', 120);
            $table->string('local_value');
            $table->string('external_value');
            $table->string('direction', 40)->default('import');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_retry_queue_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_sync_job_id')->nullable()->constrained('integration_sync_jobs')->nullOnDelete();
            $table->string('status', 40)->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('available_at')->nullable();
            $table->json('payload')->nullable();
            $table->json('last_error')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_conflicts', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_connector_id')->constrained('integration_connectors')->cascadeOnDelete();
            $table->foreignId('external_object_link_id')->nullable()->constrained('external_object_links')->nullOnDelete();
            $table->nullableMorphs('conflictable');
            $table->string('status', 40)->default('open');
            $table->json('local_snapshot')->nullable();
            $table->json('external_snapshot')->nullable();
            $table->json('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_webhook_endpoints', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_connector_id')->constrained('integration_connectors')->cascadeOnDelete();
            $table->string('direction', 40)->default('incoming');
            $table->string('event_type', 120);
            $table->string('status', 40)->default('draft');
            $table->string('secret_reference')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('integration_webhook_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_webhook_endpoint_id')->constrained('integration_webhook_endpoints')->cascadeOnDelete();
            $table->foreignId('integration_sync_job_id')->nullable()->constrained('integration_sync_jobs')->nullOnDelete();
            $table->string('status', 40)->default('queued');
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('payload')->nullable();
            $table->json('response_summary')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_rate_limits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_connector_id')->constrained('integration_connectors')->cascadeOnDelete();
            $table->string('bucket');
            $table->unsignedInteger('limit')->default(0);
            $table->unsignedInteger('remaining')->default(0);
            $table->timestamp('resets_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['integration_connector_id', 'bucket']);
        });

        Schema::create('external_catalog_sync_batches', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_connector_id')->nullable()->constrained('integration_connectors')->nullOnDelete();
            $table->string('source_type', 80)->default('external_catalog');
            $table->string('status', 40)->default('preview');
            $table->unsignedInteger('items_count')->default(0);
            $table->json('summary')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('external_catalog_sync_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('external_catalog_sync_batch_id')->constrained('external_catalog_sync_batches')->cascadeOnDelete();
            $table->string('external_object_id');
            $table->string('item_type', 60)->default('product');
            $table->string('status', 40)->default('preview');
            $table->json('payload')->nullable();
            $table->json('mapping_result')->nullable();
            $table->timestamps();
        });

        Schema::create('advanced_report_dashboards', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('dashboard_type', 80)->default('erp_pro');
            $table->string('status', 40)->default('draft');
            $table->json('layout')->nullable();
            $table->json('filters')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'slug']);
        });

        Schema::create('package_versions', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('version');
            $table->string('status', 40)->default('draft');
            $table->json('manifest')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['package_id', 'version']);
        });

        Schema::create('package_update_jobs', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('package_version_id')->nullable()->constrained('package_versions')->nullOnDelete();
            $table->string('status', 40)->default('queued');
            $table->json('plan')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('package_signing_reviews', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('package_version_id')->nullable()->constrained('package_versions')->nullOnDelete();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('pending');
            $table->string('signature_reference')->nullable();
            $table->json('checks')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'package_signing_reviews',
            'package_update_jobs',
            'package_versions',
            'advanced_report_dashboards',
            'external_catalog_sync_items',
            'external_catalog_sync_batches',
            'integration_rate_limits',
            'integration_webhook_deliveries',
            'integration_webhook_endpoints',
            'integration_conflicts',
            'integration_retry_queue_items',
            'integration_value_mappings',
            'integration_field_mappings',
            'integration_oauth_states',
            'advanced_approval_policies',
            'pos_sales',
            'pos_sessions',
            'pos_terminals',
            'accounting_posting_entries',
            'accounting_posting_batches',
            'rfq_items',
            'rfqs',
            'inventory_reservations',
            'invoice_links',
            'sales_document_lines',
            'sales_documents',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
