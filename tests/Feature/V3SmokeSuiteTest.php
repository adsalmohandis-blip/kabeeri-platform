<?php

namespace Tests\Feature;

use App\Models\BusinessProject;
use App\Models\Contact;
use App\Models\EmployeeProfile;
use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WorkflowDefinition;
use App\Modules\BusinessOperations\Services\ApprovalRequestService;
use App\Modules\BusinessOperations\Services\BusinessProjectService;
use App\Modules\BusinessOperations\Services\CrmLeadService;
use App\Modules\BusinessOperations\Services\GoodsReceiptService;
use App\Modules\BusinessOperations\Services\InvoiceService;
use App\Modules\BusinessOperations\Services\PaymentService;
use App\Modules\BusinessOperations\Services\PurchaseOrderService;
use App\Modules\BusinessOperations\Services\QuotationService;
use App\Modules\BusinessOperations\Services\QuotationToInvoiceService;
use App\Modules\BusinessOperations\Services\ReportService;
use App\Modules\BusinessOperations\Services\ServiceRequestService;
use App\Modules\BusinessOperations\Services\WorkflowHookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V3SmokeSuiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_v3_core_business_operations_flow(): void
    {
        $organization = Organization::factory()->create();
        $contact = Contact::factory()->create(['organization_id' => $organization->id]);

        $lead = app(CrmLeadService::class)->createForOrganization($organization, [
            'contact_id' => $contact->id,
            'title' => 'Smoke opportunity',
            'name' => 'Smoke Lead',
            'email' => 'smoke@example.com',
        ]);
        $serviceRequest = app(ServiceRequestService::class)->createForOrganization($organization, [
            'contact_id' => $contact->id,
            'lead_id' => $lead->id,
            'title' => 'Smoke service request',
        ]);

        $quotationService = app(QuotationService::class);
        $quotation = $quotationService->createDraft($organization, ['contact_id' => $contact->id, 'lead_id' => $lead->id]);
        $quotationService->addItem($quotation, ['name' => 'Smoke work', 'quantity' => 1, 'unit_price' => 1000]);
        $accepted = $quotationService->accept($quotationService->issue($quotation->refresh()));
        $invoice = app(QuotationToInvoiceService::class)->convert($accepted);
        app(InvoiceService::class)->issue($invoice);
        app(PaymentService::class)->recordForInvoice($invoice->refresh(), ['amount' => 1000]);

        $item = InventoryItem::factory()->create(['organization_id' => $organization->id, 'current_quantity' => 0]);
        $supplier = Supplier::factory()->create(['organization_id' => $organization->id]);
        $warehouse = Warehouse::factory()->create(['organization_id' => $organization->id]);
        $poService = app(PurchaseOrderService::class);
        $purchaseOrder = $poService->createDraft($supplier, ['warehouse_id' => $warehouse->id]);
        $purchaseOrder = $poService->addItem($purchaseOrder, [
            'inventory_item_id' => $item->id,
            'name' => $item->name,
            'quantity' => 2,
            'unit_cost' => 100,
        ]);
        $receipt = app(GoodsReceiptService::class)->createDraft($purchaseOrder);
        app(GoodsReceiptService::class)->receiveItem($receipt, $purchaseOrder->items()->firstOrFail(), 2);

        $employee = EmployeeProfile::factory()->create(['organization_id' => $organization->id]);
        $project = BusinessProject::factory()->create(['organization_id' => $organization->id]);
        $task = app(BusinessProjectService::class)->addTask($project, [
            'assigned_employee_profile_id' => $employee->id,
            'title' => 'Smoke task',
        ]);

        WorkflowDefinition::factory()->create([
            'organization_id' => $organization->id,
            'trigger_type' => 'business_task.created',
            'status' => 'active',
        ]);
        $runs = app(WorkflowHookService::class)->dispatch('business_task.created', $task);
        $approval = app(ApprovalRequestService::class)->request($task, $employee);
        app(ApprovalRequestService::class)->approve($approval);

        $report = app(ReportService::class)->createDefinition($organization, [
            'key' => 'smoke-report',
            'name' => 'Smoke Report',
            'report_type' => 'table',
        ]);
        $snapshot = app(ReportService::class)->snapshot($report, [], ['rows' => [['status' => 'ok']]]);

        $this->assertSame('closed', app(ServiceRequestService::class)->close($serviceRequest)->status);
        $this->assertSame('paid', $invoice->refresh()->payment_status);
        $this->assertSame('2.00', $item->refresh()->current_quantity);
        $this->assertCount(1, $runs);
        $this->assertSame('approved', $approval->refresh()->status);
        $this->assertSame('ok', $snapshot->data['rows'][0]['status']);
    }
}
