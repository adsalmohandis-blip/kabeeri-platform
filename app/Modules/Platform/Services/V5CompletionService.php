<?php

namespace App\Modules\Platform\Services;

use App\Models\IntegrationConnector;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class V5CompletionService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createSalesDocument(Organization $organization, array $attributes = []): int
    {
        return (int) DB::table('sales_documents')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'erp_pro_opportunity_id' => $attributes['erp_pro_opportunity_id'] ?? null,
            'document_type' => $attributes['document_type'] ?? 'proposal',
            'document_number' => $attributes['document_number'] ?? $this->nextNumber($organization, 'sales_documents', 'document_number', 'SD'),
            'title' => $attributes['title'] ?? 'V5 Sales Document',
            'status' => 'draft',
            'currency_code' => $attributes['currency_code'] ?? 'EGP',
            'subtotal' => $attributes['subtotal'] ?? 0,
            'discount_total' => 0,
            'tax_total' => 0,
            'total' => $attributes['total'] ?? ($attributes['subtotal'] ?? 0),
            'terms' => json_encode($attributes['terms'] ?? []),
            'metadata' => json_encode($attributes['metadata'] ?? []),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function addSalesDocumentLine(int $salesDocumentId, string $name, float $quantity, float $unitPrice): int
    {
        return (int) DB::table('sales_document_lines')->insertGetId([
            'sales_document_id' => $salesDocumentId,
            'name' => $name,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_total' => 0,
            'tax_total' => 0,
            'total' => $quantity * $unitPrice,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function linkInvoice(Organization $organization, Invoice $invoice, Model $source, float $amount): int
    {
        return (int) DB::table('invoice_links')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'invoice_id' => $invoice->id,
            'linkable_type' => $source->getMorphClass(),
            'linkable_id' => $source->getKey(),
            'link_type' => 'source',
            'amount' => $amount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function reserveInventory(Organization $organization, int $warehouseId, int $inventoryItemId, float $quantity): int
    {
        return (int) DB::table('inventory_reservations')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'warehouse_id' => $warehouseId,
            'inventory_item_id' => $inventoryItemId,
            'quantity' => $quantity,
            'status' => 'reserved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function createRfq(Organization $organization, ?int $supplierId = null): int
    {
        return (int) DB::table('rfqs')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'supplier_id' => $supplierId,
            'rfq_number' => $this->nextNumber($organization, 'rfqs', 'rfq_number', 'RFQ'),
            'title' => 'V5 RFQ',
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function createPostingBatch(Organization $organization, string $source = 'erp_pro'): int
    {
        return (int) DB::table('accounting_posting_batches')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'batch_number' => $this->nextNumber($organization, 'accounting_posting_batches', 'batch_number', 'POST'),
            'posting_source' => $source,
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function createPosSession(Organization $organization, ?User $user = null): int
    {
        $terminalId = (int) DB::table('pos_terminals')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'name' => 'V5 POS Terminal',
            'code' => 'POS-'.$organization->id.'-'.Str::upper(Str::random(4)),
            'status' => 'inactive',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return (int) DB::table('pos_sessions')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'pos_terminal_id' => $terminalId,
            'opened_by_user_id' => $user?->id,
            'session_number' => $this->nextNumber($organization, 'pos_sessions', 'session_number', 'POS-S'),
            'status' => 'open',
            'opened_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function createApprovalPolicy(Organization $organization, string $appliesTo): int
    {
        return (int) DB::table('advanced_approval_policies')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'name' => 'V5 Approval Policy',
            'applies_to' => $appliesTo,
            'status' => 'draft',
            'conditions' => json_encode(['min_total' => 1000]),
            'steps' => json_encode([['type' => 'approval', 'role' => 'organization-owner']]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function createOauthState(Organization $organization, IntegrationConnector $connector): int
    {
        return (int) DB::table('integration_oauth_states')->insertGetId([
            'organization_id' => $organization->id,
            'integration_connector_id' => $connector->id,
            'state' => (string) Str::uuid(),
            'status' => 'pending',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function createMappings(Organization $organization, IntegrationConnector $connector): array
    {
        $field = DB::table('integration_field_mappings')->insertGetId([
            'organization_id' => $organization->id,
            'integration_connector_id' => $connector->id,
            'local_model' => 'App\\Models\\Product',
            'external_object_type' => 'product',
            'local_field' => 'name',
            'external_field' => 'title',
            'direction' => 'import',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $value = DB::table('integration_value_mappings')->insertGetId([
            'organization_id' => $organization->id,
            'integration_connector_id' => $connector->id,
            'mapping_group' => 'status',
            'local_value' => 'active',
            'external_value' => 'published',
            'direction' => 'import',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [(int) $field, (int) $value];
    }

    public function createRetryConflictWebhookAndLimit(Organization $organization, IntegrationConnector $connector, int $syncJobId): array
    {
        $retry = DB::table('integration_retry_queue_items')->insertGetId([
            'organization_id' => $organization->id,
            'integration_sync_job_id' => $syncJobId,
            'status' => 'pending',
            'available_at' => now(),
            'payload' => json_encode(['retry' => true]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $conflict = DB::table('integration_conflicts')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'integration_connector_id' => $connector->id,
            'status' => 'open',
            'local_snapshot' => json_encode(['name' => 'Local']),
            'external_snapshot' => json_encode(['name' => 'External']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $endpoint = DB::table('integration_webhook_endpoints')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'integration_connector_id' => $connector->id,
            'direction' => 'incoming',
            'event_type' => 'product.updated',
            'status' => 'draft',
            'secret_reference' => 'vault://webhook/demo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $delivery = DB::table('integration_webhook_deliveries')->insertGetId([
            'organization_id' => $organization->id,
            'integration_webhook_endpoint_id' => $endpoint,
            'integration_sync_job_id' => $syncJobId,
            'status' => 'queued',
            'payload' => json_encode(['event' => 'product.updated']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $limit = DB::table('integration_rate_limits')->insertGetId([
            'organization_id' => $organization->id,
            'integration_connector_id' => $connector->id,
            'bucket' => 'default',
            'limit' => 100,
            'remaining' => 100,
            'resets_at' => now()->addHour(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [(int) $retry, (int) $conflict, (int) $endpoint, (int) $delivery, (int) $limit];
    }

    public function createExternalCatalogPreview(Organization $organization, IntegrationConnector $connector): int
    {
        $batch = (int) DB::table('external_catalog_sync_batches')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'integration_connector_id' => $connector->id,
            'status' => 'preview',
            'items_count' => 1,
            'summary' => json_encode(['mode' => 'preview']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('external_catalog_sync_items')->insert([
            'external_catalog_sync_batch_id' => $batch,
            'external_object_id' => 'EXT-PRODUCT-1',
            'item_type' => 'product',
            'status' => 'preview',
            'payload' => json_encode(['name' => 'External Product']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $batch;
    }

    public function createAdvancedDashboard(Organization $organization, string $slug = 'v5-erp-pro'): int
    {
        return (int) DB::table('advanced_report_dashboards')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'organization_id' => $organization->id,
            'name' => 'V5 ERP Pro Dashboard',
            'slug' => $slug,
            'dashboard_type' => 'erp_pro',
            'status' => 'draft',
            'layout' => json_encode(['widgets' => ['pipeline', 'revenue', 'sync_health']]),
            'filters' => json_encode(['period' => 'month']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function createPackageOps(Package $package, ?User $reviewer = null): array
    {
        $version = DB::table('package_versions')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'package_id' => $package->id,
            'version' => '5.0.0',
            'status' => 'draft',
            'manifest' => json_encode(['v5' => true]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $job = DB::table('package_update_jobs')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'package_id' => $package->id,
            'package_version_id' => $version,
            'status' => 'queued',
            'plan' => json_encode(['strategy' => 'review_first']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $review = DB::table('package_signing_reviews')->insertGetId([
            'ulid' => (string) Str::ulid(),
            'package_id' => $package->id,
            'package_version_id' => $version,
            'reviewed_by_user_id' => $reviewer?->id,
            'status' => 'pending',
            'signature_reference' => 'vault://signing/pending',
            'checks' => json_encode(['manifest' => 'pending', 'signature' => 'pending']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [(int) $version, (int) $job, (int) $review];
    }

    private function nextNumber(Organization $organization, string $table, string $column, string $prefix): string
    {
        $next = DB::table($table)->where('organization_id', $organization->id)->count() + 1;

        return $prefix.'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
