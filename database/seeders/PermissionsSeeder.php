<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Organization', 'slug' => 'organization.view', 'group' => 'organization', 'risk_level' => 'low'],
            ['name' => 'Manage Organization', 'slug' => 'organization.manage', 'group' => 'organization', 'risk_level' => 'high'],
            ['name' => 'Manage Organization Members', 'slug' => 'organization.members.manage', 'group' => 'organization', 'risk_level' => 'high'],
            ['name' => 'Manage Organization Settings', 'slug' => 'organization.settings.manage', 'group' => 'organization', 'risk_level' => 'high'],

            ['name' => 'View Site', 'slug' => 'site.view', 'group' => 'site', 'risk_level' => 'low'],
            ['name' => 'Create Site', 'slug' => 'site.create', 'group' => 'site', 'risk_level' => 'medium'],
            ['name' => 'Edit Site', 'slug' => 'site.edit', 'group' => 'site', 'risk_level' => 'medium'],
            ['name' => 'Delete Site', 'slug' => 'site.delete', 'group' => 'site', 'risk_level' => 'high'],
            ['name' => 'Manage Site Settings', 'slug' => 'site.settings.manage', 'group' => 'site', 'risk_level' => 'high'],

            ['name' => 'View Content', 'slug' => 'content.view', 'group' => 'content', 'risk_level' => 'low'],
            ['name' => 'Create Content', 'slug' => 'content.create', 'group' => 'content', 'risk_level' => 'low'],
            ['name' => 'Edit Content', 'slug' => 'content.edit', 'group' => 'content', 'risk_level' => 'low'],
            ['name' => 'Publish Content', 'slug' => 'content.publish', 'group' => 'content', 'risk_level' => 'medium'],
            ['name' => 'Delete Content', 'slug' => 'content.delete', 'group' => 'content', 'risk_level' => 'medium'],

            ['name' => 'View Media', 'slug' => 'media.view', 'group' => 'media', 'risk_level' => 'low'],
            ['name' => 'Upload Media', 'slug' => 'media.upload', 'group' => 'media', 'risk_level' => 'low'],
            ['name' => 'Edit Media', 'slug' => 'media.edit', 'group' => 'media', 'risk_level' => 'medium'],
            ['name' => 'Delete Media', 'slug' => 'media.delete', 'group' => 'media', 'risk_level' => 'medium'],

            ['name' => 'View Company', 'slug' => 'company.view', 'group' => 'company', 'risk_level' => 'low'],
            ['name' => 'Create Company', 'slug' => 'company.create', 'group' => 'company', 'risk_level' => 'medium'],
            ['name' => 'Edit Company', 'slug' => 'company.edit', 'group' => 'company', 'risk_level' => 'medium'],
            ['name' => 'Manage Company', 'slug' => 'company.manage', 'group' => 'company', 'risk_level' => 'high'],

            ['name' => 'View Business Profile', 'slug' => 'business_profile.view', 'group' => 'rabet', 'risk_level' => 'low'],
            ['name' => 'Edit Business Profile', 'slug' => 'business_profile.edit', 'group' => 'rabet', 'risk_level' => 'medium'],
            ['name' => 'Submit Verification', 'slug' => 'verification.submit', 'group' => 'rabet', 'risk_level' => 'medium'],

            ['name' => 'View Activity Log', 'slug' => 'activity_log.view', 'group' => 'system', 'risk_level' => 'medium'],
            ['name' => 'View Settings', 'slug' => 'settings.view', 'group' => 'system', 'risk_level' => 'medium'],
            ['name' => 'View Feature Flags', 'slug' => 'feature_flags.view', 'group' => 'system', 'risk_level' => 'high'],

            ['name' => 'View CMS Menus', 'slug' => 'menu.view', 'group' => 'cms', 'risk_level' => 'low'],
            ['name' => 'Manage CMS Menus', 'slug' => 'menu.manage', 'group' => 'cms', 'risk_level' => 'medium'],
            ['name' => 'View Redirects', 'slug' => 'redirect.view', 'group' => 'cms', 'risk_level' => 'low'],
            ['name' => 'Manage Redirects', 'slug' => 'redirect.manage', 'group' => 'cms', 'risk_level' => 'medium'],
            ['name' => 'Manage SEO', 'slug' => 'seo.manage', 'group' => 'cms', 'risk_level' => 'medium'],

            ['name' => 'View Forms', 'slug' => 'form.view', 'group' => 'forms', 'risk_level' => 'low'],
            ['name' => 'Create Forms', 'slug' => 'form.create', 'group' => 'forms', 'risk_level' => 'medium'],
            ['name' => 'Edit Forms', 'slug' => 'form.edit', 'group' => 'forms', 'risk_level' => 'medium'],
            ['name' => 'Delete Forms', 'slug' => 'form.delete', 'group' => 'forms', 'risk_level' => 'high'],
            ['name' => 'View Form Submissions', 'slug' => 'form.submissions.view', 'group' => 'forms', 'risk_level' => 'medium'],
            ['name' => 'Export Form Submissions', 'slug' => 'form.submissions.export', 'group' => 'forms', 'risk_level' => 'high'],

            ['name' => 'View Migrations', 'slug' => 'migration.view', 'group' => 'migration', 'risk_level' => 'medium'],
            ['name' => 'Create Migrations', 'slug' => 'migration.create', 'group' => 'migration', 'risk_level' => 'medium'],
            ['name' => 'Preview Migrations', 'slug' => 'migration.preview', 'group' => 'migration', 'risk_level' => 'medium'],
            ['name' => 'Run Migrations', 'slug' => 'migration.run', 'group' => 'migration', 'risk_level' => 'high'],
            ['name' => 'Rollback Migrations', 'slug' => 'migration.rollback', 'group' => 'migration', 'risk_level' => 'high'],

            ['name' => 'View Theme Catalog', 'slug' => 'theme.catalog.view', 'group' => 'theme', 'risk_level' => 'low'],
            ['name' => 'Apply Theme', 'slug' => 'theme.apply', 'group' => 'theme', 'risk_level' => 'medium'],
            ['name' => 'View Theme Recipes', 'slug' => 'theme.recipe.view', 'group' => 'theme', 'risk_level' => 'low'],
            ['name' => 'Apply Theme Recipes', 'slug' => 'theme.recipe.apply', 'group' => 'theme', 'risk_level' => 'medium'],

            ['name' => 'View Package Catalog', 'slug' => 'package.catalog.view', 'group' => 'package', 'risk_level' => 'low'],
            ['name' => 'Install Packages', 'slug' => 'package.install', 'group' => 'package', 'risk_level' => 'high'],
            ['name' => 'Uninstall Packages', 'slug' => 'package.uninstall', 'group' => 'package', 'risk_level' => 'high'],

            ['name' => 'View Products', 'slug' => 'product.view', 'group' => 'commerce_lite', 'risk_level' => 'low'],
            ['name' => 'Create Products', 'slug' => 'product.create', 'group' => 'commerce_lite', 'risk_level' => 'medium'],
            ['name' => 'Edit Products', 'slug' => 'product.edit', 'group' => 'commerce_lite', 'risk_level' => 'medium'],
            ['name' => 'View Orders', 'slug' => 'order.view', 'group' => 'commerce_lite', 'risk_level' => 'medium'],
            ['name' => 'Manage Coupons', 'slug' => 'coupon.manage', 'group' => 'commerce_lite', 'risk_level' => 'medium'],

            ['name' => 'View External Sources', 'slug' => 'external_source.view', 'group' => 'external_sources', 'risk_level' => 'medium'],
            ['name' => 'Manage External Sources', 'slug' => 'external_source.manage', 'group' => 'external_sources', 'risk_level' => 'high'],

            ['name' => 'View CRM', 'slug' => 'crm.view', 'group' => 'crm', 'risk_level' => 'low'],
            ['name' => 'Manage Contacts', 'slug' => 'contact.manage', 'group' => 'crm', 'risk_level' => 'medium'],
            ['name' => 'Manage Leads', 'slug' => 'lead.manage', 'group' => 'crm', 'risk_level' => 'medium'],
            ['name' => 'Manage Pipelines', 'slug' => 'pipeline.manage', 'group' => 'crm', 'risk_level' => 'medium'],
            ['name' => 'Manage CRM Activities', 'slug' => 'crm_activity.manage', 'group' => 'crm', 'risk_level' => 'medium'],
            ['name' => 'Manage Service Requests', 'slug' => 'service_request.manage', 'group' => 'service', 'risk_level' => 'medium'],

            ['name' => 'View Quotations', 'slug' => 'quotation.view', 'group' => 'sales', 'risk_level' => 'low'],
            ['name' => 'Manage Quotations', 'slug' => 'quotation.manage', 'group' => 'sales', 'risk_level' => 'medium'],
            ['name' => 'Issue Quotations', 'slug' => 'quotation.issue', 'group' => 'sales', 'risk_level' => 'medium'],
            ['name' => 'Accept Quotations', 'slug' => 'quotation.accept', 'group' => 'sales', 'risk_level' => 'high'],
            ['name' => 'View Invoices', 'slug' => 'invoice.view', 'group' => 'finance', 'risk_level' => 'low'],
            ['name' => 'Manage Invoices', 'slug' => 'invoice.manage', 'group' => 'finance', 'risk_level' => 'high'],
            ['name' => 'Record Payments', 'slug' => 'payment.record', 'group' => 'finance', 'risk_level' => 'high'],

            ['name' => 'View Inventory', 'slug' => 'inventory.view', 'group' => 'inventory', 'risk_level' => 'low'],
            ['name' => 'Manage Inventory Items', 'slug' => 'inventory_item.manage', 'group' => 'inventory', 'risk_level' => 'medium'],
            ['name' => 'Manage Warehouses', 'slug' => 'warehouse.manage', 'group' => 'inventory', 'risk_level' => 'medium'],
            ['name' => 'Record Stock Movements', 'slug' => 'stock_movement.record', 'group' => 'inventory', 'risk_level' => 'high'],
            ['name' => 'View Purchasing', 'slug' => 'purchase.view', 'group' => 'purchasing', 'risk_level' => 'low'],
            ['name' => 'Manage Suppliers', 'slug' => 'supplier.manage', 'group' => 'purchasing', 'risk_level' => 'medium'],
            ['name' => 'Manage Purchase Orders', 'slug' => 'purchase_order.manage', 'group' => 'purchasing', 'risk_level' => 'medium'],
            ['name' => 'Receive Goods', 'slug' => 'goods_receipt.manage', 'group' => 'purchasing', 'risk_level' => 'high'],

            ['name' => 'View Accounting', 'slug' => 'accounting.view', 'group' => 'accounting', 'risk_level' => 'medium'],
            ['name' => 'Manage Chart of Accounts', 'slug' => 'account.manage', 'group' => 'accounting', 'risk_level' => 'high'],
            ['name' => 'Manage Journal Entries', 'slug' => 'journal_entry.manage', 'group' => 'accounting', 'risk_level' => 'high'],
            ['name' => 'Post Journal Entries', 'slug' => 'journal_entry.post', 'group' => 'accounting', 'risk_level' => 'high'],

            ['name' => 'View People', 'slug' => 'people.view', 'group' => 'people', 'risk_level' => 'low'],
            ['name' => 'Manage Employees', 'slug' => 'employee.manage', 'group' => 'people', 'risk_level' => 'medium'],
            ['name' => 'Manage Departments', 'slug' => 'department.manage', 'group' => 'people', 'risk_level' => 'medium'],
            ['name' => 'Manage Positions', 'slug' => 'position.manage', 'group' => 'people', 'risk_level' => 'medium'],
            ['name' => 'Manage Evaluations', 'slug' => 'evaluation.manage', 'group' => 'people', 'risk_level' => 'medium'],

            ['name' => 'View Projects', 'slug' => 'project.view', 'group' => 'projects', 'risk_level' => 'low'],
            ['name' => 'Manage Projects', 'slug' => 'project.manage', 'group' => 'projects', 'risk_level' => 'medium'],
            ['name' => 'Manage Tasks', 'slug' => 'task.manage', 'group' => 'projects', 'risk_level' => 'medium'],

            ['name' => 'View Workflows', 'slug' => 'workflow.view', 'group' => 'workflow', 'risk_level' => 'low'],
            ['name' => 'Manage Workflow Definitions', 'slug' => 'workflow.manage', 'group' => 'workflow', 'risk_level' => 'high'],
            ['name' => 'Run Workflows', 'slug' => 'workflow.run', 'group' => 'workflow', 'risk_level' => 'high'],
            ['name' => 'Manage Approvals', 'slug' => 'approval.manage', 'group' => 'workflow', 'risk_level' => 'high'],

            ['name' => 'View Reports', 'slug' => 'report.view', 'group' => 'reports', 'risk_level' => 'low'],
            ['name' => 'Manage Reports', 'slug' => 'report.manage', 'group' => 'reports', 'risk_level' => 'medium'],
            ['name' => 'Manage Dashboard Widgets', 'slug' => 'dashboard_widget.manage', 'group' => 'reports', 'risk_level' => 'medium'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission + ['description' => null],
            );
        }
    }
}
