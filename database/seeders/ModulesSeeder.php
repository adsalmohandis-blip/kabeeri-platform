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
        ];

        foreach ($modules as $module) {
            Module::query()->updateOrCreate(
                ['key' => $module['key']],
                $module,
            );
        }
    }
}
