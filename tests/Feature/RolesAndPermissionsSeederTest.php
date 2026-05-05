<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesAndPermissionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_and_permissions_seeders_are_idempotent_and_attach_expected_permissions(): void
    {
        $this->seed([
            PermissionsSeeder::class,
            RolesSeeder::class,
        ]);

        $this->assertSame(58, Permission::query()->count());
        $this->assertSame(7, Role::query()->count());

        $platformRole = Role::query()->where('slug', 'platform-super-admin')->firstOrFail();
        $ownerRole = Role::query()->where('slug', 'organization-owner')->firstOrFail();
        $adminRole = Role::query()->where('slug', 'organization-admin')->firstOrFail();
        $editorRole = Role::query()->where('slug', 'editor')->firstOrFail();

        $this->assertTrue($platformRole->permissions()->where('slug', 'feature_flags.view')->exists());
        $this->assertTrue($editorRole->permissions()->where('slug', 'content.create')->exists());
        $this->assertFalse($editorRole->permissions()->where('slug', 'organization.manage')->exists());

        $expectedV2Permissions = [
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

        foreach ($expectedV2Permissions as $slug) {
            $this->assertDatabaseHas('permissions', ['slug' => $slug]);
            $this->assertTrue($ownerRole->permissions()->where('slug', $slug)->exists());
            $this->assertTrue($adminRole->permissions()->where('slug', $slug)->exists());
        }

        $this->assertFalse($editorRole->permissions()->where('slug', 'migration.run')->exists());
        $this->assertFalse($editorRole->permissions()->where('slug', 'migration.rollback')->exists());

        $this->seed([
            PermissionsSeeder::class,
            RolesSeeder::class,
        ]);

        $this->assertSame(58, Permission::query()->count());
        $this->assertSame(7, Role::query()->count());
    }
}
