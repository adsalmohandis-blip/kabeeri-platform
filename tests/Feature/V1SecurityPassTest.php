<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\MembershipRoleAssignment;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\ActivityLogger;
use App\Policies\FeatureFlagOverridePolicy;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class V1SecurityPassTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_has_no_role_or_tenant_scope_columns(): void
    {
        $this->assertFalse(Schema::hasColumn('users', 'role'));
        $this->assertFalse(Schema::hasColumn('users', 'organization_id'));
        $this->assertFalse(Schema::hasColumn('users', 'company_id'));
        $this->assertFalse(Schema::hasColumn('users', 'site_id'));
    }

    public function test_activity_logs_resource_is_read_only_in_admin_routes(): void
    {
        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $log = app(ActivityLogger::class)->log(
            action: 'security.test',
            organizationId: $organization->id,
            siteId: $site->id,
            actorUserId: $owner->id,
            subject: $site,
        );

        $this->actingAs($owner);

        $this->get('/admin/activity-logs')->assertOk();
        $this->get(route('filament.admin.resources.activity-logs.view', ['record' => $log]))->assertOk();
        $this->get('/admin/activity-logs/create')->assertNotFound();
        $this->get('/admin/activity-logs/'.$log->id.'/edit')->assertNotFound();
    }

    public function test_feature_flag_override_create_requires_owner_or_manage_settings_permission(): void
    {
        $this->seed([PermissionsSeeder::class, RolesSeeder::class]);

        $owner = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);

        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $member->id,
            'membership_type' => 'employee',
            'status' => 'active',
        ]);

        $viewerRole = Role::query()->where('slug', 'viewer')->firstOrFail();

        MembershipRoleAssignment::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $viewerRole->id,
            'scope_type' => 'organization',
            'scope_id' => $organization->id,
            'created_by' => $owner->id,
        ]);

        $policy = app(FeatureFlagOverridePolicy::class);

        $this->assertTrue($policy->create($owner));
        $this->assertFalse($policy->create($member));
    }

    public function test_outsider_cannot_access_foreign_tenant_records_across_v1_scope(): void
    {
        $ownerA = User::factory()->create();
        $ownerB = User::factory()->create();
        $organizationA = Organization::factory()->create(['owner_user_id' => $ownerA->id]);
        $companyA = Company::factory()->create(['organization_id' => $organizationA->id]);
        $siteA = Site::factory()->create([
            'organization_id' => $organizationA->id,
            'company_id' => $companyA->id,
        ]);
        $contentTypeA = ContentType::factory()->create([
            'organization_id' => $organizationA->id,
            'site_id' => $siteA->id,
        ]);
        $entryA = ContentEntry::factory()->create([
            'organization_id' => $organizationA->id,
            'site_id' => $siteA->id,
            'content_type_id' => $contentTypeA->id,
        ]);
        $settingA = Setting::query()->create([
            'organization_id' => $organizationA->id,
            'scope_type' => 'organization',
            'scope_id' => $organizationA->id,
            'key' => 'security.test',
            'value' => ['enabled' => true],
            'value_type' => 'json',
            'is_encrypted' => false,
        ]);

        $logA = app(ActivityLogger::class)->log(
            action: 'organization.updated',
            organizationId: $organizationA->id,
            companyId: $companyA->id,
            siteId: $siteA->id,
            actorUserId: $ownerA->id,
            subject: $organizationA,
        );

        $this->actingAs($ownerB);

        $organizationView = $this->get(route('filament.admin.resources.organizations.view', ['record' => $organizationA]));
        $companyEdit = $this->get(route('filament.admin.resources.companies.edit', ['record' => $companyA]));
        $siteEdit = $this->get(route('filament.admin.resources.sites.edit', ['record' => $siteA]));
        $contentEdit = $this->get(route('filament.admin.resources.content-entries.edit', ['record' => $entryA]));
        $settingEdit = $this->get(route('filament.admin.resources.settings.edit', ['record' => $settingA]));
        $logView = $this->get(route('filament.admin.resources.activity-logs.view', ['record' => $logA]));

        $this->assertContains($organizationView->getStatusCode(), [403, 404]);
        $this->assertContains($companyEdit->getStatusCode(), [403, 404]);
        $this->assertContains($siteEdit->getStatusCode(), [403, 404]);
        $this->assertContains($contentEdit->getStatusCode(), [403, 404]);
        $this->assertContains($settingEdit->getStatusCode(), [403, 404]);
        $this->assertContains($logView->getStatusCode(), [403, 404]);
    }
}
