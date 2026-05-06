<?php

namespace Tests\Feature;

use App\Models\BusinessProject;
use App\Models\Contact;
use App\Models\DashboardWidget;
use App\Models\EmployeeProfile;
use App\Models\InventoryItem;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Product;
use App\Models\SalesPipeline;
use App\Models\SalesPipelineStage;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Modules\BusinessOperations\Services\ApprovalRequestService;
use App\Modules\BusinessOperations\Services\BusinessProjectService;
use App\Modules\BusinessOperations\Services\CrmLeadService;
use App\Modules\BusinessOperations\Services\InventoryItemService;
use App\Modules\BusinessOperations\Services\PurchaseOrderService;
use App\Modules\BusinessOperations\Services\QuotationService;
use App\Modules\BusinessOperations\Services\SalesPipelineService;
use App\Modules\BusinessOperations\Services\ServiceRequestService;
use Database\Seeders\V1DemoSeeder;
use Database\Seeders\V3DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class V3SecurityTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_keeps_tenant_and_role_fields_out_of_user_records(): void
    {
        $columns = Schema::getColumnListing('users');

        $this->assertNotContains('organization_id', $columns);
        $this->assertNotContains('company_id', $columns);
        $this->assertNotContains('site_id', $columns);
        $this->assertNotContains('role', $columns);
    }

    public function test_v3_services_reject_cross_tenant_record_links(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();

        $foreignContact = Contact::factory()->create(['organization_id' => $otherOrganization->id]);
        $foreignLead = Lead::factory()->create(['organization_id' => $otherOrganization->id]);
        $foreignProduct = Product::factory()->create(['organization_id' => $otherOrganization->id]);
        $foreignWarehouse = Warehouse::factory()->create(['organization_id' => $otherOrganization->id]);
        $foreignItem = InventoryItem::factory()->create(['organization_id' => $otherOrganization->id]);
        $foreignEmployee = EmployeeProfile::factory()->create(['organization_id' => $otherOrganization->id]);

        $this->expectValidationFailure(fn () => app(CrmLeadService::class)->createForOrganization($organization, [
            'contact_id' => $foreignContact->id,
            'title' => 'Foreign contact',
        ]));

        $this->expectValidationFailure(fn () => app(ServiceRequestService::class)->createForOrganization($organization, [
            'lead_id' => $foreignLead->id,
            'title' => 'Foreign lead',
        ]));

        $this->expectValidationFailure(fn () => app(QuotationService::class)->createDraft($organization, [
            'contact_id' => $foreignContact->id,
        ]));

        $this->expectValidationFailure(fn () => app(InventoryItemService::class)->createForOrganization($organization, [
            'product_id' => $foreignProduct->id,
            'name' => 'Foreign product item',
            'sku' => 'FOREIGN-PRODUCT',
        ]));

        $supplier = Supplier::factory()->create(['organization_id' => $organization->id]);
        $purchaseOrder = app(PurchaseOrderService::class)->createDraft($supplier);

        $this->expectValidationFailure(fn () => app(PurchaseOrderService::class)->createDraft($supplier, [
            'warehouse_id' => $foreignWarehouse->id,
        ]));

        $this->expectValidationFailure(fn () => app(PurchaseOrderService::class)->addItem($purchaseOrder, [
            'inventory_item_id' => $foreignItem->id,
            'name' => 'Foreign item',
        ]));

        $lead = Lead::factory()->create(['organization_id' => $organization->id]);
        $foreignPipeline = SalesPipeline::factory()->create(['organization_id' => $otherOrganization->id]);
        $foreignStage = SalesPipelineStage::factory()->create(['sales_pipeline_id' => $foreignPipeline->id]);

        $this->expectValidationFailure(fn () => app(SalesPipelineService::class)->moveLeadToStage($lead, $foreignStage));

        $project = BusinessProject::factory()->create(['organization_id' => $organization->id]);

        $this->expectValidationFailure(fn () => app(BusinessProjectService::class)->addTask($project, [
            'assigned_employee_profile_id' => $foreignEmployee->id,
            'title' => 'Foreign assignee',
        ]));

        $task = app(BusinessProjectService::class)->addTask($project, ['title' => 'Approval subject']);

        $this->expectValidationFailure(fn () => app(ApprovalRequestService::class)->request($task, $foreignEmployee));
    }

    public function test_v3_demo_seed_settings_do_not_use_sensitive_key_names(): void
    {
        $this->seed(V1DemoSeeder::class);
        $this->seed(V3DemoSeeder::class);

        $settings = DashboardWidget::query()
            ->where('key', 'v3-demo-widget')
            ->firstOrFail()
            ->settings;

        $this->assertArrayNotHasKey('token', $settings);
        $this->assertArrayNotHasKey('secret', $settings);
        $this->assertArrayNotHasKey('api_key', $settings);
        $this->assertArrayHasKey('warehouse_code', $settings);
    }

    private function expectValidationFailure(callable $callback): void
    {
        try {
            $callback();
        } catch (ValidationException) {
            $this->addToAssertionCount(1);

            return;
        }

        $this->fail('Expected a validation exception for a cross-tenant V3 operation.');
    }
}
