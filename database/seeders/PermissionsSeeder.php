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

            ['name' => 'Manage Operating Modes', 'slug' => 'operating_mode.manage', 'group' => 'platform_operations', 'risk_level' => 'high'],
            ['name' => 'View Cloud Sites', 'slug' => 'cloud_site.view', 'group' => 'cloud', 'risk_level' => 'low'],
            ['name' => 'Manage Cloud Sites', 'slug' => 'cloud_site.manage', 'group' => 'cloud', 'risk_level' => 'high'],
            ['name' => 'View Cloud Domains', 'slug' => 'cloud_domain.view', 'group' => 'cloud', 'risk_level' => 'low'],
            ['name' => 'Manage Cloud Domains', 'slug' => 'cloud_domain.manage', 'group' => 'cloud', 'risk_level' => 'high'],
            ['name' => 'View Cloud Backups', 'slug' => 'cloud_backup.view', 'group' => 'cloud', 'risk_level' => 'medium'],
            ['name' => 'Manage Cloud Backups', 'slug' => 'cloud_backup.manage', 'group' => 'cloud', 'risk_level' => 'high'],
            ['name' => 'View Mall Listings', 'slug' => 'mall_listing.view', 'group' => 'mall', 'risk_level' => 'low'],
            ['name' => 'Manage Mall Listings', 'slug' => 'mall_listing.manage', 'group' => 'mall', 'risk_level' => 'medium'],
            ['name' => 'Publish Mall Listings', 'slug' => 'mall_listing.publish', 'group' => 'mall', 'risk_level' => 'high'],
            ['name' => 'Manage Mall Sync', 'slug' => 'mall_sync.manage', 'group' => 'mall', 'risk_level' => 'high'],
            ['name' => 'View Moderation', 'slug' => 'moderation.view', 'group' => 'moderation', 'risk_level' => 'medium'],
            ['name' => 'Manage Moderation', 'slug' => 'moderation.manage', 'group' => 'moderation', 'risk_level' => 'high'],
            ['name' => 'View Reviews', 'slug' => 'review.view', 'group' => 'trust', 'risk_level' => 'low'],
            ['name' => 'Manage Reviews', 'slug' => 'review.manage', 'group' => 'trust', 'risk_level' => 'medium'],
            ['name' => 'Manage Trust Badges', 'slug' => 'trust_badge.manage', 'group' => 'trust', 'risk_level' => 'high'],
            ['name' => 'Manage Legal Partners', 'slug' => 'legal_partner.manage', 'group' => 'partners', 'risk_level' => 'high'],
            ['name' => 'Manage Agency Partners', 'slug' => 'agency_partner.manage', 'group' => 'partners', 'risk_level' => 'high'],
            ['name' => 'View Internal Marketplace', 'slug' => 'marketplace.view', 'group' => 'marketplace', 'risk_level' => 'low'],
            ['name' => 'Govern Internal Marketplace', 'slug' => 'marketplace.govern', 'group' => 'marketplace', 'risk_level' => 'high'],
            ['name' => 'View Creator Profiles', 'slug' => 'creator_profile.view', 'group' => 'creator', 'risk_level' => 'low'],
            ['name' => 'Manage Creator Profiles', 'slug' => 'creator_profile.manage', 'group' => 'creator', 'risk_level' => 'medium'],
            ['name' => 'View Work Network', 'slug' => 'work_network.view', 'group' => 'work_network', 'risk_level' => 'low'],
            ['name' => 'Manage Work Network', 'slug' => 'work_network.manage', 'group' => 'work_network', 'risk_level' => 'medium'],
            ['name' => 'View Academy', 'slug' => 'academy.view', 'group' => 'academy', 'risk_level' => 'low'],
            ['name' => 'Manage Academy Badges', 'slug' => 'academy_badge.manage', 'group' => 'academy', 'risk_level' => 'medium'],
            ['name' => 'Manage Growth Referrals', 'slug' => 'growth_referral.manage', 'group' => 'growth', 'risk_level' => 'high'],
            ['name' => 'Manage Partner Storefronts', 'slug' => 'partner_storefront.manage', 'group' => 'partners', 'risk_level' => 'medium'],
            ['name' => 'View ERP Pro', 'slug' => 'erp_pro.view', 'group' => 'erp_pro', 'risk_level' => 'medium'],
            ['name' => 'Manage ERP Pro', 'slug' => 'erp_pro.manage', 'group' => 'erp_pro', 'risk_level' => 'high'],
            ['name' => 'Manage Contracts', 'slug' => 'contract.manage', 'group' => 'contracts', 'risk_level' => 'high'],
            ['name' => 'Manage Helpdesk', 'slug' => 'helpdesk.manage', 'group' => 'helpdesk', 'risk_level' => 'medium'],
            ['name' => 'Manage Commissions', 'slug' => 'commission.manage', 'group' => 'partners', 'risk_level' => 'high'],
            ['name' => 'Review Partner Payouts', 'slug' => 'partner_payout.review', 'group' => 'partners', 'risk_level' => 'high'],
            ['name' => 'View Integration Hub', 'slug' => 'integration_hub.view', 'group' => 'integrations', 'risk_level' => 'medium'],
            ['name' => 'Manage Integration Hub', 'slug' => 'integration_hub.manage', 'group' => 'integrations', 'risk_level' => 'high'],
            ['name' => 'Manage Integration Mappings', 'slug' => 'integration_mapping.manage', 'group' => 'integrations', 'risk_level' => 'high'],
            ['name' => 'Manage Integration Webhooks', 'slug' => 'integration_webhook.manage', 'group' => 'integrations', 'risk_level' => 'high'],
            ['name' => 'Resolve Integration Conflicts', 'slug' => 'integration_conflict.resolve', 'group' => 'integrations', 'risk_level' => 'high'],
            ['name' => 'View Billing Usage', 'slug' => 'billing_usage.view', 'group' => 'billing', 'risk_level' => 'high'],
            ['name' => 'Manage Package Operations', 'slug' => 'package_operations.manage', 'group' => 'package', 'risk_level' => 'high'],
            ['name' => 'Review Package Signing', 'slug' => 'package_signing.review', 'group' => 'package', 'risk_level' => 'high'],
            ['name' => 'Manage Enterprise Security', 'slug' => 'enterprise_security.manage', 'group' => 'enterprise_security', 'risk_level' => 'high'],
            ['name' => 'Manage SIEM Exports', 'slug' => 'siem_export.manage', 'group' => 'enterprise_security', 'risk_level' => 'high'],
            ['name' => 'Govern Developer Marketplace', 'slug' => 'developer_marketplace.govern', 'group' => 'developer_marketplace', 'risk_level' => 'high'],
            ['name' => 'Manage Data Platform', 'slug' => 'data_platform.manage', 'group' => 'data_platform', 'risk_level' => 'high'],
            ['name' => 'Manage GRC', 'slug' => 'grc.manage', 'group' => 'grc', 'risk_level' => 'high'],
            ['name' => 'Manage Industry Solutions', 'slug' => 'industry_solution.manage', 'group' => 'industry_solutions', 'risk_level' => 'high'],
            ['name' => 'Manage AI Co-builder', 'slug' => 'ai_cobuilder.manage', 'group' => 'ai', 'risk_level' => 'high'],
            ['name' => 'Manage Enterprise API Gateway', 'slug' => 'api_gateway.manage', 'group' => 'api', 'risk_level' => 'high'],
            ['name' => 'Manage Privacy Retention', 'slug' => 'privacy_retention.manage', 'group' => 'privacy', 'risk_level' => 'high'],
            ['name' => 'Manage Enterprise Performance', 'slug' => 'performance_scale.manage', 'group' => 'platform_operations', 'risk_level' => 'high'],
            ['name' => 'View Mobile Apps', 'slug' => 'mobile_app.view', 'group' => 'mobile', 'risk_level' => 'low'],
            ['name' => 'Manage Mobile Apps', 'slug' => 'mobile_app.manage', 'group' => 'mobile', 'risk_level' => 'high'],
            ['name' => 'Manage Mobile Devices', 'slug' => 'mobile_device.manage', 'group' => 'mobile', 'risk_level' => 'medium'],
            ['name' => 'View Desktop Clients', 'slug' => 'desktop_client.view', 'group' => 'desktop', 'risk_level' => 'low'],
            ['name' => 'Manage Desktop Clients', 'slug' => 'desktop_client.manage', 'group' => 'desktop', 'risk_level' => 'medium'],
            ['name' => 'Manage Desktop Sync', 'slug' => 'desktop_sync.manage', 'group' => 'desktop', 'risk_level' => 'high'],
            ['name' => 'Manage Desktop File Queue', 'slug' => 'desktop_file_queue.manage', 'group' => 'desktop', 'risk_level' => 'medium'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission + ['description' => null],
            );
        }
    }
}
