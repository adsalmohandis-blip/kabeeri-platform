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

        $this->assertSame(28, Permission::query()->count());
        $this->assertSame(7, Role::query()->count());

        $platformRole = Role::query()->where('slug', 'platform-super-admin')->firstOrFail();
        $editorRole = Role::query()->where('slug', 'editor')->firstOrFail();

        $this->assertTrue($platformRole->permissions()->where('slug', 'feature_flags.view')->exists());
        $this->assertTrue($editorRole->permissions()->where('slug', 'content.create')->exists());
        $this->assertFalse($editorRole->permissions()->where('slug', 'organization.manage')->exists());

        $this->seed([
            PermissionsSeeder::class,
            RolesSeeder::class,
        ]);

        $this->assertSame(28, Permission::query()->count());
        $this->assertSame(7, Role::query()->count());
    }
}
