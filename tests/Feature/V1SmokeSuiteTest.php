<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\MembershipRoleAssignment;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Models\UserProfile;
use App\Modules\CMS\Actions\CreateContentEntry;
use App\Modules\CMS\Actions\PublishContentEntry;
use App\Modules\Core\Actions\CreateOrganizationWithOwnerMembership;
use App\Modules\Core\Services\ActivityLogger;
use App\Modules\Core\Services\MediaService;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V1SmokeSuiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_exist_with_profile(): void
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
        $this->assertDatabaseHas('user_profiles', [
            'id' => $profile->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_core_workspace_smoke_flow_from_organization_to_public_page(): void
    {
        $this->seed([PermissionsSeeder::class, RolesSeeder::class]);

        $owner = User::factory()->create();
        $editor = User::factory()->create();

        $organization = app(CreateOrganizationWithOwnerMembership::class)(
            [
                'name' => 'Smoke Org',
                'slug' => 'smoke-org',
                'account_type' => 'business',
                'status' => 'active',
                'locale' => 'ar',
                'timezone' => 'Africa/Cairo',
            ],
            $owner,
        );

        $this->assertDatabaseHas('organizations', ['id' => $organization->id]);
        $this->assertDatabaseHas('organization_memberships', [
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'membership_type' => 'owner',
            'status' => 'active',
        ]);

        $company = Company::factory()->create([
            'organization_id' => $organization->id,
            'trade_name' => 'Smoke Company',
            'slug' => 'smoke-company',
        ]);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'organization_id' => $organization->id,
        ]);

        $site = Site::factory()->create([
            'organization_id' => $organization->id,
            'company_id' => $company->id,
            'name' => 'Smoke App',
            'slug' => 'smoke-app',
            'created_by' => $owner->id,
        ]);
        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'organization_id' => $organization->id,
        ]);

        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $editor->id,
            'membership_type' => 'employee',
            'status' => 'active',
        ]);
        $editorRole = Role::query()->where('slug', 'editor')->firstOrFail();

        MembershipRoleAssignment::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $editorRole->id,
            'scope_type' => 'site',
            'scope_id' => $site->id,
            'created_by' => $owner->id,
        ]);

        $this->assertDatabaseHas('membership_role_assignments', [
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $editorRole->id,
            'scope_type' => 'site',
            'scope_id' => $site->id,
        ]);

        $pageType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Page',
            'slug' => 'page',
        ]);

        $entry = app(CreateContentEntry::class)(
            actor: $editor,
            site: $site,
            contentType: $pageType,
            attributes: [
                'title' => 'Smoke Home',
                'slug' => 'smoke-home',
                'body' => 'Smoke page body',
                'status' => 'draft',
                'visibility' => 'public',
            ],
        );

        $this->assertDatabaseHas('content_entries', [
            'id' => $entry->id,
            'status' => 'draft',
            'visibility' => 'public',
        ]);

        $published = app(PublishContentEntry::class)(
            actor: $editor,
            entry: $entry,
        );

        $this->assertSame('published', $published->status);
        $this->assertNotNull($published->published_at);

        $this->get('/app/'.$site->slug.'/'.$published->slug)
            ->assertOk()
            ->assertSee('Smoke Home');
    }

    public function test_media_metadata_and_activity_log_can_be_stored(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $user->id]);
        $company = Company::factory()->create(['organization_id' => $organization->id]);
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
            'company_id' => $company->id,
            'created_by' => $user->id,
        ]);

        $media = app(MediaService::class)->createAsset([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'company_id' => $company->id,
            'uploaded_by' => $user->id,
            'disk' => 'public',
            'path' => 'smoke/media/cover.jpg',
            'relative_path' => 'smoke/media/cover.jpg',
            'filename' => 'cover.jpg',
            'original_filename' => 'cover.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size_bytes' => 4096,
            'width' => 1200,
            'height' => 630,
            'visibility' => 'organization_only',
            'alt_text' => 'Smoke cover image',
            'caption' => 'Smoke caption',
            'checksum' => 'smoke-checksum',
            'metadata' => ['source' => 'smoke_test'],
        ]);

        $this->assertDatabaseHas('media_assets', [
            'id' => $media->id,
            'organization_id' => $organization->id,
            'relative_path' => 'smoke/media/cover.jpg',
        ]);

        $log = app(ActivityLogger::class)->log(
            action: 'smoke.media.created',
            organizationId: $organization->id,
            companyId: $company->id,
            siteId: $site->id,
            actorUserId: $user->id,
            subject: $media,
            metadata: ['source' => 'smoke_test'],
        );

        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'organization_id' => $organization->id,
            'action' => 'smoke.media.created',
            'actor_user_id' => $user->id,
        ]);
    }

    public function test_tenant_isolation_prevents_cross_organization_access(): void
    {
        $ownerA = User::factory()->create();
        $ownerB = User::factory()->create();

        $orgA = Organization::factory()->create(['owner_user_id' => $ownerA->id]);
        $companyA = Company::factory()->create(['organization_id' => $orgA->id]);
        $siteA = Site::factory()->create([
            'organization_id' => $orgA->id,
            'company_id' => $companyA->id,
            'slug' => 'tenant-a-app',
        ]);
        $typeA = ContentType::factory()->create([
            'organization_id' => $orgA->id,
            'site_id' => $siteA->id,
            'slug' => 'page',
        ]);
        $entryA = ContentEntry::factory()->create([
            'organization_id' => $orgA->id,
            'site_id' => $siteA->id,
            'content_type_id' => $typeA->id,
        ]);

        $logA = app(ActivityLogger::class)->log(
            action: 'organization.updated',
            organizationId: $orgA->id,
            companyId: $companyA->id,
            siteId: $siteA->id,
            actorUserId: $ownerA->id,
            subject: $orgA,
        );

        $this->actingAs($ownerB);

        $companyEdit = $this->get('/admin/companies/'.$companyA->id.'/edit');
        $entryEdit = $this->get('/admin/content-entries/'.$entryA->id.'/edit');
        $logView = $this->get(route('filament.admin.resources.activity-logs.view', ['record' => $logA]));

        $this->assertContains($companyEdit->getStatusCode(), [403, 404]);
        $this->assertContains($entryEdit->getStatusCode(), [403, 404]);
        $this->assertContains($logView->getStatusCode(), [403, 404]);
    }
}
