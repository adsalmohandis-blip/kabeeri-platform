<?php

namespace Tests\Feature;

use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\MembershipRoleAssignment;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Modules\CMS\Actions\CreateContentEntry;
use App\Modules\CMS\Actions\PublishContentEntry;
use App\Modules\CMS\Actions\UpdateContentEntry;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CmsActionsAndPoliciesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_editor_can_create_edit_and_publish_content_with_activity_logs(): void
    {
        $this->seed([PermissionsSeeder::class, RolesSeeder::class]);

        $owner = User::factory()->create();
        $editor = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
            'created_by' => $owner->id,
        ]);
        $contentType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Page',
            'slug' => 'page',
        ]);

        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $editor->id,
            'membership_type' => 'employee',
        ]);
        $role = Role::query()->where('slug', 'editor')->firstOrFail();

        MembershipRoleAssignment::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $role->id,
            'scope_type' => 'site',
            'scope_id' => $site->id,
            'created_by' => $owner->id,
        ]);

        $this->assertTrue(
            Gate::forUser($editor)->allows('createForSite', [ContentEntry::class, $site]),
        );

        $entry = app(CreateContentEntry::class)(
            actor: $editor,
            site: $site,
            contentType: $contentType,
            attributes: [
                'title' => 'About Us',
                'slug' => 'about-us',
                'body' => 'Initial page body.',
                'visibility' => 'public',
            ],
        );

        $updatedEntry = app(UpdateContentEntry::class)(
            actor: $editor,
            entry: $entry,
            attributes: [
                'body' => 'Updated page body.',
            ],
        );

        $publishedEntry = app(PublishContentEntry::class)(
            actor: $editor,
            entry: $updatedEntry,
        );

        $this->assertSame('published', $publishedEntry->status);
        $this->assertNotNull($publishedEntry->published_at);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'content_entry.created',
            'subject_id' => $entry->id,
            'actor_user_id' => $editor->id,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'content_entry.updated',
            'subject_id' => $entry->id,
            'actor_user_id' => $editor->id,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'content_entry.published',
            'subject_id' => $entry->id,
            'actor_user_id' => $editor->id,
        ]);
    }

    public function test_unauthorized_user_cannot_publish_content(): void
    {
        $this->seed([PermissionsSeeder::class, RolesSeeder::class]);

        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $contentType = ContentType::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Post',
            'slug' => 'post',
        ]);

        $entry = ContentEntry::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'content_type_id' => $contentType->id,
            'status' => 'draft',
            'visibility' => 'public',
        ]);

        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $viewer->id,
            'membership_type' => 'employee',
        ]);
        $viewerRole = Role::query()->where('slug', 'viewer')->firstOrFail();

        MembershipRoleAssignment::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'role_id' => $viewerRole->id,
            'scope_type' => 'site',
            'scope_id' => $site->id,
            'created_by' => $owner->id,
        ]);

        $this->assertFalse(Gate::forUser($viewer)->allows('publish', $entry));

        $this->expectException(AuthorizationException::class);

        app(PublishContentEntry::class)(
            actor: $viewer,
            entry: $entry,
        );
    }
}
