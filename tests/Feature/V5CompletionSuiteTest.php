<?php

namespace Tests\Feature;

use App\Filament\Resources\ErpProOpportunities\ErpProOpportunityResource;
use App\Filament\Resources\IntegrationConnectors\IntegrationConnectorResource;
use App\Models\IntegrationConnector;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Package;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Modules\BusinessOperations\Services\ErpProService;
use App\Modules\Platform\Services\IntegrationHubService;
use App\Modules\Platform\Services\V5CompletionService;
use App\Modules\Rabet\Services\CommissionPayoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class V5CompletionSuiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_remaining_v5_foundation_tables_exist(): void
    {
        foreach ([
            'sales_documents',
            'sales_document_lines',
            'invoice_links',
            'inventory_reservations',
            'rfqs',
            'rfq_items',
            'accounting_posting_batches',
            'accounting_posting_entries',
            'pos_terminals',
            'pos_sessions',
            'pos_sales',
            'advanced_approval_policies',
            'integration_oauth_states',
            'integration_field_mappings',
            'integration_value_mappings',
            'integration_retry_queue_items',
            'integration_conflicts',
            'integration_webhook_endpoints',
            'integration_webhook_deliveries',
            'integration_rate_limits',
            'external_catalog_sync_batches',
            'external_catalog_sync_items',
            'advanced_report_dashboards',
            'package_versions',
            'package_update_jobs',
            'package_signing_reviews',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing V5 completion table [{$table}].");
        }
    }

    public function test_remaining_v5_operational_flow_creates_safe_records(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $opportunity = app(ErpProService::class)->createOpportunity($organization, ['name' => 'Completion Opportunity']);
        $service = app(V5CompletionService::class);

        $salesDocument = $service->createSalesDocument($organization, [
            'erp_pro_opportunity_id' => $opportunity->id,
            'subtotal' => 1200,
        ]);
        $line = $service->addSalesDocumentLine($salesDocument, 'Implementation', 2, 600);

        $invoice = Invoice::factory()->create(['organization_id' => $organization->id]);
        $invoiceLink = $service->linkInvoice($organization, $invoice, $opportunity, 1200);

        $warehouse = Warehouse::factory()->create(['organization_id' => $organization->id]);
        $item = InventoryItem::factory()->create(['organization_id' => $organization->id]);
        $supplier = Supplier::factory()->create(['organization_id' => $organization->id]);
        $reservation = $service->reserveInventory($organization, $warehouse->id, $item->id, 3);
        $rfq = $service->createRfq($organization, $supplier->id);
        DB::table('rfq_items')->insert([
            'rfq_id' => $rfq,
            'inventory_item_id' => $item->id,
            'name' => 'Reserved Item',
            'quantity' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $postingBatch = $service->createPostingBatch($organization);
        $posSession = $service->createPosSession($organization, $user);
        $approvalPolicy = $service->createApprovalPolicy($organization, 'sales_documents');

        $hub = app(IntegrationHubService::class);
        $connector = $hub->registerConnector($organization, [
            'key' => 'completion-connector',
            'name' => 'Completion Connector',
            'provider' => 'demo',
        ]);
        $job = $hub->queuePreviewSync($organization, $connector, ['scope' => 'completion']);
        $oauth = $service->createOauthState($organization, $connector);
        [$fieldMapping, $valueMapping] = $service->createMappings($organization, $connector);
        [$retry, $conflict, $webhookEndpoint, $webhookDelivery, $rateLimit] = $service->createRetryConflictWebhookAndLimit($organization, $connector, $job->id);
        $catalogBatch = $service->createExternalCatalogPreview($organization, $connector);
        $dashboard = $service->createAdvancedDashboard($organization, 'completion-dashboard');

        $package = Package::factory()->create();
        [$packageVersion, $packageJob, $packageReview] = $service->createPackageOps($package, $user);

        $commission = app(CommissionPayoutService::class);
        $plan = $commission->createPlan($organization, 'Completion Commission Plan', 0.15);
        $event = $commission->recordEvent($organization, $plan, null, $opportunity, 2000);
        $payout = $commission->schedulePayout($organization, null, (float) $event->commission_amount);

        $this->assertDatabaseHas('sales_documents', ['id' => $salesDocument, 'status' => 'draft']);
        $this->assertDatabaseHas('sales_document_lines', ['id' => $line]);
        $this->assertDatabaseHas('invoice_links', ['id' => $invoiceLink]);
        $this->assertDatabaseHas('inventory_reservations', ['id' => $reservation, 'status' => 'reserved']);
        $this->assertDatabaseHas('rfqs', ['id' => $rfq, 'status' => 'draft']);
        $this->assertDatabaseHas('accounting_posting_batches', ['id' => $postingBatch, 'status' => 'draft']);
        $this->assertDatabaseHas('pos_sessions', ['id' => $posSession, 'status' => 'open']);
        $this->assertDatabaseHas('advanced_approval_policies', ['id' => $approvalPolicy, 'status' => 'draft']);
        $this->assertDatabaseHas('integration_oauth_states', ['id' => $oauth, 'status' => 'pending']);
        $this->assertDatabaseHas('integration_field_mappings', ['id' => $fieldMapping]);
        $this->assertDatabaseHas('integration_value_mappings', ['id' => $valueMapping]);
        $this->assertDatabaseHas('integration_retry_queue_items', ['id' => $retry, 'status' => 'pending']);
        $this->assertDatabaseHas('integration_conflicts', ['id' => $conflict, 'status' => 'open']);
        $this->assertDatabaseHas('integration_webhook_endpoints', ['id' => $webhookEndpoint, 'secret_reference' => 'vault://webhook/demo']);
        $this->assertDatabaseHas('integration_webhook_deliveries', ['id' => $webhookDelivery, 'status' => 'queued']);
        $this->assertDatabaseHas('integration_rate_limits', ['id' => $rateLimit, 'remaining' => 100]);
        $this->assertDatabaseHas('external_catalog_sync_batches', ['id' => $catalogBatch, 'status' => 'preview']);
        $this->assertDatabaseHas('advanced_report_dashboards', ['id' => $dashboard, 'dashboard_type' => 'erp_pro']);
        $this->assertDatabaseHas('package_versions', ['id' => $packageVersion, 'status' => 'draft']);
        $this->assertDatabaseHas('package_update_jobs', ['id' => $packageJob, 'status' => 'queued']);
        $this->assertDatabaseHas('package_signing_reviews', ['id' => $packageReview, 'status' => 'pending']);
        $this->assertSame('300.00', $event->commission_amount);
        $this->assertSame('pending_review', $payout->status);
    }

    public function test_v5_filament_resources_are_registered_and_tenant_scoped(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        OrganizationMembership::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'status' => 'active',
        ]);

        $owned = app(ErpProService::class)->createOpportunity($organization, ['name' => 'Owned']);
        app(ErpProService::class)->createOpportunity(Organization::factory()->create(), ['name' => 'Other']);
        $connector = IntegrationConnector::query()->create([
            'organization_id' => $organization->id,
            'key' => 'owned',
            'name' => 'Owned Connector',
            'provider' => 'demo',
        ]);
        IntegrationConnector::query()->create([
            'organization_id' => Organization::factory()->create()->id,
            'key' => 'other',
            'name' => 'Other Connector',
            'provider' => 'demo',
        ]);

        $this->actingAs($user);

        $this->assertArrayHasKey('index', ErpProOpportunityResource::getPages());
        $this->assertArrayHasKey('index', IntegrationConnectorResource::getPages());
        $this->assertSame([$owned->id], ErpProOpportunityResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$connector->id], IntegrationConnectorResource::getEloquentQuery()->pluck('id')->all());
    }
}
