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
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission + ['description' => null],
            );
        }
    }
}
