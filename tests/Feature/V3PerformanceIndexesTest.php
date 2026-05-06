<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class V3PerformanceIndexesTest extends TestCase
{
    use RefreshDatabase;

    public function test_v3_operational_tables_have_composite_tenant_indexes(): void
    {
        $this->assertIndexCovers('contacts', ['organization_id', 'status', 'contact_type']);
        $this->assertIndexCovers('leads', ['organization_id', 'status', 'priority']);
        $this->assertIndexCovers('leads', ['organization_id', 'sales_pipeline_id', 'sales_pipeline_stage_id', 'status']);
        $this->assertIndexCovers('quotations', ['organization_id', 'status', 'created_at']);
        $this->assertIndexCovers('invoices', ['organization_id', 'status', 'payment_status']);
        $this->assertIndexCovers('payments', ['organization_id', 'invoice_id', 'status']);
        $this->assertIndexCovers('inventory_items', ['organization_id', 'status', 'item_type']);
        $this->assertIndexCovers('stock_movements', ['organization_id', 'inventory_item_id', 'created_at']);
        $this->assertIndexCovers('purchase_orders', ['organization_id', 'status', 'supplier_id']);
        $this->assertIndexCovers('goods_receipts', ['organization_id', 'status', 'purchase_order_id']);
        $this->assertIndexCovers('employee_profiles', ['organization_id', 'status', 'department_id']);
        $this->assertIndexCovers('business_tasks', ['organization_id', 'status', 'assigned_employee_profile_id']);
        $this->assertIndexCovers('workflow_definitions', ['organization_id', 'trigger_type', 'status']);
        $this->assertIndexCovers('approval_requests', ['organization_id', 'status', 'approver_employee_profile_id']);
        $this->assertIndexCovers('report_definitions', ['organization_id', 'status', 'report_type']);
        $this->assertIndexCovers('dashboard_widgets', ['organization_id', 'status', 'widget_type']);
    }

    /**
     * @param  list<string>  $expectedColumns
     */
    private function assertIndexCovers(string $table, array $expectedColumns): void
    {
        $indexes = DB::select("PRAGMA index_list('{$table}')");

        foreach ($indexes as $index) {
            $columns = collect(DB::select("PRAGMA index_info('{$index->name}')"))
                ->sortBy('seqno')
                ->pluck('name')
                ->all();

            if ($columns === $expectedColumns) {
                $this->addToAssertionCount(1);

                return;
            }
        }

        $this->fail(sprintf(
            'Expected %s to have an index covering [%s].',
            $table,
            implode(', ', $expectedColumns),
        ));
    }
}
