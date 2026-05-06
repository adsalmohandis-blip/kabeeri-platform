<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'contact_type'], 'v3_contacts_org_status_type_idx');
        });

        Schema::table('leads', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'priority'], 'v3_leads_org_status_priority_idx');
            $table->index(['organization_id', 'sales_pipeline_id', 'sales_pipeline_stage_id', 'status'], 'v3_leads_pipeline_status_idx');
        });

        Schema::table('quotations', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'created_at'], 'v3_quotes_org_status_created_idx');
        });

        Schema::table('invoices', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'payment_status'], 'v3_invoices_org_status_payment_idx');
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->index(['organization_id', 'invoice_id', 'status'], 'v3_payments_org_invoice_status_idx');
        });

        Schema::table('inventory_items', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'item_type'], 'v3_inventory_org_status_type_idx');
        });

        Schema::table('stock_movements', function (Blueprint $table): void {
            $table->index(['organization_id', 'inventory_item_id', 'created_at'], 'v3_stock_org_item_created_idx');
        });

        Schema::table('purchase_orders', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'supplier_id'], 'v3_po_org_status_supplier_idx');
        });

        Schema::table('goods_receipts', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'purchase_order_id'], 'v3_receipts_org_status_po_idx');
        });

        Schema::table('employee_profiles', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'department_id'], 'v3_employees_org_status_dept_idx');
        });

        Schema::table('business_tasks', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'assigned_employee_profile_id'], 'v3_tasks_org_status_assignee_idx');
        });

        Schema::table('workflow_definitions', function (Blueprint $table): void {
            $table->index(['organization_id', 'trigger_type', 'status'], 'v3_workflows_org_trigger_status_idx');
        });

        Schema::table('approval_requests', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'approver_employee_profile_id'], 'v3_approvals_org_status_approver_idx');
        });

        Schema::table('report_definitions', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'report_type'], 'v3_reports_org_status_type_idx');
        });

        Schema::table('dashboard_widgets', function (Blueprint $table): void {
            $table->index(['organization_id', 'status', 'widget_type'], 'v3_widgets_org_status_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dashboard_widgets', function (Blueprint $table): void {
            $table->dropIndex('v3_widgets_org_status_type_idx');
        });

        Schema::table('report_definitions', function (Blueprint $table): void {
            $table->dropIndex('v3_reports_org_status_type_idx');
        });

        Schema::table('approval_requests', function (Blueprint $table): void {
            $table->dropIndex('v3_approvals_org_status_approver_idx');
        });

        Schema::table('workflow_definitions', function (Blueprint $table): void {
            $table->dropIndex('v3_workflows_org_trigger_status_idx');
        });

        Schema::table('business_tasks', function (Blueprint $table): void {
            $table->dropIndex('v3_tasks_org_status_assignee_idx');
        });

        Schema::table('employee_profiles', function (Blueprint $table): void {
            $table->dropIndex('v3_employees_org_status_dept_idx');
        });

        Schema::table('goods_receipts', function (Blueprint $table): void {
            $table->dropIndex('v3_receipts_org_status_po_idx');
        });

        Schema::table('purchase_orders', function (Blueprint $table): void {
            $table->dropIndex('v3_po_org_status_supplier_idx');
        });

        Schema::table('stock_movements', function (Blueprint $table): void {
            $table->dropIndex('v3_stock_org_item_created_idx');
        });

        Schema::table('inventory_items', function (Blueprint $table): void {
            $table->dropIndex('v3_inventory_org_status_type_idx');
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->dropIndex('v3_payments_org_invoice_status_idx');
        });

        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropIndex('v3_invoices_org_status_payment_idx');
        });

        Schema::table('quotations', function (Blueprint $table): void {
            $table->dropIndex('v3_quotes_org_status_created_idx');
        });

        Schema::table('leads', function (Blueprint $table): void {
            $table->dropIndex('v3_leads_pipeline_status_idx');
            $table->dropIndex('v3_leads_org_status_priority_idx');
        });

        Schema::table('contacts', function (Blueprint $table): void {
            $table->dropIndex('v3_contacts_org_status_type_idx');
        });
    }
};
