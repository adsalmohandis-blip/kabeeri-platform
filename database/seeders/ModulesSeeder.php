<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModulesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $modules = [
            [
                'key' => 'core',
                'name' => 'Core',
                'description' => 'Core platform module',
                'module_type' => 'core',
                'status' => 'active',
                'version' => '1.0.0',
                'requires' => null,
            ],
            [
                'key' => 'cms',
                'name' => 'CMS',
                'description' => 'Content management module',
                'module_type' => 'cms',
                'status' => 'active',
                'version' => '1.0.0',
                'requires' => ['core'],
            ],
            [
                'key' => 'media',
                'name' => 'Media',
                'description' => 'Media asset module',
                'module_type' => 'cms',
                'status' => 'active',
                'version' => '1.0.0',
                'requires' => ['core'],
            ],
            [
                'key' => 'rabet',
                'name' => 'Rabet Foundation',
                'description' => 'Business profile foundation module',
                'module_type' => 'industry',
                'status' => 'active',
                'version' => '1.0.0',
                'requires' => ['core'],
            ],
            [
                'key' => 'erp_pro',
                'name' => 'ERP Pro Foundation',
                'description' => 'V5 ERP Pro operational foundations',
                'module_type' => 'business',
                'status' => 'active',
                'version' => '5.0.0',
                'requires' => ['core'],
            ],
            [
                'key' => 'integration_hub',
                'name' => 'Integration Hub',
                'description' => 'V5 connector, credential reference, and preview sync foundations',
                'module_type' => 'integration',
                'status' => 'active',
                'version' => '5.0.0',
                'requires' => ['core'],
            ],
            [
                'key' => 'enterprise_platform',
                'name' => 'Enterprise Platform',
                'description' => 'V6 enterprise security, data, GRC, marketplace, AI, API, and industry foundations',
                'module_type' => 'enterprise',
                'status' => 'active',
                'version' => '6.0.0',
                'requires' => ['core', 'integration_hub'],
            ],
            [
                'key' => 'mobile_platform',
                'name' => 'Mobile Platform',
                'description' => 'V7 mobile app configuration, device registry, push tokens, and public/auth APIs',
                'module_type' => 'mobile',
                'status' => 'active',
                'version' => '7.0.0',
                'requires' => ['core'],
            ],
            [
                'key' => 'desktop_platform',
                'name' => 'Desktop Platform',
                'description' => 'V8 desktop client registry, sync sessions, pull, push dry-run, conflicts, and file queue foundations',
                'module_type' => 'desktop',
                'status' => 'active',
                'version' => '8.0.0',
                'requires' => ['core'],
            ],
        ];

        foreach ($modules as $module) {
            Module::query()->updateOrCreate(
                ['key' => $module['key']],
                $module,
            );
        }
    }
}
