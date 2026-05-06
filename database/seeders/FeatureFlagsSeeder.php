<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $flags = [
            'enable_cms',
            'enable_media_library',
            'enable_theme_foundation',
            'enable_package_foundation',
            'enable_rabet_foundation',
            'enable_onboarding_basic',
            'enable_activity_logs',
            'enable_cms_menus',
            'enable_cms_redirects',
            'enable_seo_tools',
            'enable_forms',
            'enable_lead_capture',
            'enable_wordpress_import',
            'enable_theme_catalog',
            'enable_theme_app_recipes',
            'enable_package_catalog',
            'enable_plugin_bundles',
            'enable_commerce_lite',
            'enable_external_source_registry',
            'enable_crm_operations',
            'enable_service_requests',
            'enable_quotations',
            'enable_invoicing',
            'enable_manual_payments',
            'enable_inventory_lite',
            'enable_purchasing',
            'enable_accounting_starter',
            'enable_people_operations',
            'enable_projects_tasks',
            'enable_workflow_basic',
            'enable_reports_basic',
            'enable_dashboard_widgets',
        ];

        foreach ($flags as $key) {
            FeatureFlag::query()->updateOrCreate(
                ['key' => $key],
                [
                    'description' => null,
                    'default_value' => false,
                    'status' => 'active',
                ],
            );
        }
    }
}
