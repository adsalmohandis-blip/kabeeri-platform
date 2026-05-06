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

        $v2OwnerAdminPermissions = [
            'menu.view',
            'menu.manage',
            'redirect.view',
            'redirect.manage',
            'seo.manage',
            'form.view',
            'form.create',
            'form.edit',
            'form.delete',
            'form.submissions.view',
            'form.submissions.export',
            'migration.view',
            'migration.create',
            'migration.preview',
            'migration.run',
            'migration.rollback',
            'theme.catalog.view',
            'theme.apply',
            'theme.recipe.view',
            'theme.recipe.apply',
            'package.catalog.view',
            'package.install',
            'package.uninstall',
            'product.view',
            'product.create',
            'product.edit',
            'order.view',
            'coupon.manage',
            'external_source.view',
            'external_source.manage',
        ];

        $v3OwnerAdminPermissions = [
            'crm.view',
            'contact.manage',
            'lead.manage',
            'pipeline.manage',
            'crm_activity.manage',
            'service_request.manage',
            'quotation.view',
            'quotation.manage',
            'quotation.issue',
            'quotation.accept',
            'invoice.view',
            'invoice.manage',
            'payment.record',
            'inventory.view',
            'inventory_item.manage',
            'warehouse.manage',
            'stock_movement.record',
            'purchase.view',
            'supplier.manage',
            'purchase_order.manage',
            'goods_receipt.manage',
            'accounting.view',
            'account.manage',
            'journal_entry.manage',
            'journal_entry.post',
            'people.view',
            'employee.manage',
            'department.manage',
            'position.manage',
            'evaluation.manage',
            'project.view',
            'project.manage',
            'task.manage',
            'workflow.view',
            'workflow.manage',
            'workflow.run',
            'approval.manage',
            'report.view',
            'report.manage',
            'dashboard_widget.manage',
        ];

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
                'permissions' => array_merge([
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
                ], $v2OwnerAdminPermissions, $v3OwnerAdminPermissions),
            ],
            [
                'name' => 'Organization Admin',
                'slug' => 'organization-admin',
                'scope' => 'organization',
                'permissions' => array_merge([
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
                ], $v2OwnerAdminPermissions, $v3OwnerAdminPermissions),
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
