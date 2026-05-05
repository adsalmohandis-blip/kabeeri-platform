<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionsSeeder::class);

        $permissionIdsBySlug = Permission::query()
            ->pluck('id', 'slug')
            ->all();

        $roleDefinitions = [
            [
                'name' => 'Platform Super Admin',
                'slug' => 'platform-super-admin',
                'scope' => 'platform',
                'permissions' => array_keys($permissionIdsBySlug),
            ],
            [
                'name' => 'Organization Owner',
                'slug' => 'organization-owner',
                'scope' => 'organization',
                'permissions' => [
                    'organization.view',
                    'organization.manage',
                    'organization.members.manage',
                    'organization.settings.manage',
                    'site.view',
                    'site.create',
                    'site.edit',
                    'site.delete',
                    'site.settings.manage',
                    'content.view',
                    'content.create',
                    'content.edit',
                    'content.publish',
                    'content.delete',
                    'media.view',
                    'media.upload',
                    'media.edit',
                    'media.delete',
                    'company.view',
                    'company.create',
                    'company.edit',
                    'company.manage',
                    'business_profile.view',
                    'business_profile.edit',
                    'verification.submit',
                    'activity_log.view',
                    'settings.view',
                    'feature_flags.view',
                ],
            ],
            [
                'name' => 'Organization Admin',
                'slug' => 'organization-admin',
                'scope' => 'organization',
                'permissions' => [
                    'organization.view',
                    'organization.members.manage',
                    'organization.settings.manage',
                    'site.view',
                    'site.create',
                    'site.edit',
                    'site.delete',
                    'site.settings.manage',
                    'content.view',
                    'content.create',
                    'content.edit',
                    'content.publish',
                    'content.delete',
                    'media.view',
                    'media.upload',
                    'media.edit',
                    'media.delete',
                    'company.view',
                    'company.create',
                    'company.edit',
                    'company.manage',
                    'business_profile.view',
                    'business_profile.edit',
                    'verification.submit',
                    'activity_log.view',
                    'settings.view',
                ],
            ],
            [
                'name' => 'Site Admin',
                'slug' => 'site-admin',
                'scope' => 'site',
                'permissions' => [
                    'site.view',
                    'site.create',
                    'site.edit',
                    'site.delete',
                    'site.settings.manage',
                    'content.view',
                    'content.create',
                    'content.edit',
                    'content.publish',
                    'content.delete',
                    'media.view',
                    'media.upload',
                    'media.edit',
                    'media.delete',
                    'activity_log.view',
                ],
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'scope' => 'site',
                'permissions' => [
                    'site.view',
                    'content.view',
                    'content.create',
                    'content.edit',
                    'content.publish',
                    'media.view',
                    'media.upload',
                ],
            ],
            [
                'name' => 'Media Manager',
                'slug' => 'media-manager',
                'scope' => 'site',
                'permissions' => [
                    'site.view',
                    'content.view',
                    'media.view',
                    'media.upload',
                    'media.edit',
                    'media.delete',
                ],
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'scope' => 'site',
                'permissions' => [
                    'organization.view',
                    'site.view',
                    'content.view',
                    'media.view',
                    'company.view',
                    'business_profile.view',
                    'activity_log.view',
                    'settings.view',
                ],
            ],
        ];

        foreach ($roleDefinitions as $definition) {
            $role = Role::query()->updateOrCreate(
                [
                    'organization_id' => null,
                    'company_id' => null,
                    'site_id' => null,
                    'slug' => $definition['slug'],
                    'scope' => $definition['scope'],
                ],
                [
                    'name' => $definition['name'],
                    'is_system' => true,
                ],
            );

            $permissionIds = array_values(array_filter(
                array_map(
                    static fn (string $slug): ?int => $permissionIdsBySlug[$slug] ?? null,
                    $definition['permissions'],
                ),
            ));

            $role->permissions()->sync($permissionIds);
        }
    }
}
