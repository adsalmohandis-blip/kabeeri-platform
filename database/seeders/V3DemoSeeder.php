<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\BusinessProject;
use App\Models\Contact;
use App\Models\DashboardWidget;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\InventoryItem;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Quotation;
use App\Models\ReportDefinition;
use App\Models\ServiceRequest;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WorkflowDefinition;
use App\Modules\BusinessOperations\Services\ChartOfAccountsService;
use Illuminate\Database\Seeder;

class V3DemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->first();

        if (! $organization) {
            return;
        }

        $contact = Contact::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'email' => 'v3-demo-contact@example.com'],
            [
                'display_name' => 'V3 Demo Contact',
                'first_name' => 'V3',
                'last_name' => 'Demo Contact',
                'contact_type' => 'customer',
                'status' => 'active',
                'source' => 'demo',
            ],
        );

        $lead = Lead::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'email' => 'v3-demo-lead@example.com'],
            [
                'contact_id' => $contact->id,
                'title' => 'V3 Demo Opportunity',
                'name' => 'V3 Demo Lead',
                'source' => 'demo',
                'status' => 'new',
                'priority' => 'normal',
                'expected_value' => 15000,
                'currency_code' => 'EGP',
            ],
        );

        ServiceRequest::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'request_number' => 'SR-DEMO-001'],
            [
                'contact_id' => $contact->id,
                'lead_id' => $lead->id,
                'title' => 'V3 Demo Service Request',
                'request_type' => 'onboarding',
                'status' => 'new',
                'priority' => 'normal',
            ],
        );

        Quotation::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'quotation_number' => 'QT-DEMO-001'],
            [
                'contact_id' => $contact->id,
                'lead_id' => $lead->id,
                'title' => 'V3 Demo Quotation',
                'status' => 'draft',
                'currency_code' => 'EGP',
                'subtotal' => 15000,
                'total' => 15000,
            ],
        );

        $warehouse = Warehouse::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v3-demo-main-warehouse'],
            [
                'name' => 'V3 Demo Main Warehouse',
                'code' => 'V3MAIN',
                'status' => 'active',
                'is_default' => true,
            ],
        );

        InventoryItem::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'sku' => 'V3-DEMO-ITEM'],
            [
                'name' => 'V3 Demo Inventory Item',
                'item_type' => 'stocked',
                'unit_of_measure' => 'pcs',
                'status' => 'active',
                'current_quantity' => 10,
                'reorder_level' => 5,
            ],
        );

        Supplier::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v3-demo-supplier'],
            [
                'name' => 'V3 Demo Supplier',
                'supplier_code' => 'V3SUP',
                'status' => 'active',
            ],
        );

        app(ChartOfAccountsService::class)->seedStarterAccounts($organization);
        Account::query()->where('organization_id', $organization->id)->where('code', '1000')->update(['metadata' => ['demo' => true]]);

        $department = Department::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v3-demo-sales'],
            ['name' => 'V3 Demo Sales', 'status' => 'active'],
        );

        EmployeeProfile::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'employee_number' => 'EMP-DEMO-001'],
            [
                'department_id' => $department->id,
                'full_name' => 'V3 Demo Employee',
                'email' => 'v3-demo-employee@example.com',
                'status' => 'active',
                'employment_type' => 'full_time',
            ],
        );

        BusinessProject::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v3-demo-project'],
            ['name' => 'V3 Demo Project', 'status' => 'active'],
        );

        WorkflowDefinition::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'v3-demo-approval'],
            [
                'name' => 'V3 Demo Approval',
                'trigger_type' => 'manual',
                'status' => 'active',
                'steps' => [['type' => 'approval', 'label' => 'Owner approval']],
            ],
        );

        $report = ReportDefinition::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'key' => 'v3-demo-report'],
            [
                'name' => 'V3 Demo Report',
                'report_type' => 'table',
                'status' => 'active',
                'columns' => ['name', 'status'],
            ],
        );

        DashboardWidget::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'key' => 'v3-demo-widget'],
            [
                'report_definition_id' => $report->id,
                'title' => 'V3 Demo Widget',
                'widget_type' => 'metric',
                'status' => 'active',
                'settings' => ['size' => 'sm', 'warehouse_code' => $warehouse->code ?? 'demo'],
            ],
        );
    }
}
