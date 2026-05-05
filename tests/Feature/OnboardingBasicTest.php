<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Actions\CreateFirstWorkspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_first_organization_and_app_with_owner_membership(): void
    {
        $user = User::factory()->create();

        $result = app(CreateFirstWorkspace::class)(
            $user,
            [
                'organization_name' => 'Kabeeri Organization',
                'site_name' => 'Main App',
            ],
        );

        $organization = $result['organization'];
        $site = $result['site'];

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'owner_user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('organization_memberships', [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'membership_type' => 'owner',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'organization_id' => $organization->id,
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'actor_user_id' => $user->id,
            'action' => 'onboarding.workspace.created',
        ]);
    }

    public function test_user_can_optionally_create_company_draft_and_seed_basic_cms_page(): void
    {
        $user = User::factory()->create();

        $result = app(CreateFirstWorkspace::class)(
            $user,
            [
                'organization_name' => 'Kabeeri Workspace',
                'site_name' => 'Portal App',
                'create_company_draft' => true,
                'company_name' => 'Kabeeri Company',
                'seed_basic_cms_page' => true,
                'seed_page_title' => 'Home',
            ],
        );

        $organization = $result['organization'];
        $company = $result['company'];
        $site = $result['site'];
        $seededEntry = $result['seeded_content_entry'];

        $this->assertNotNull($company);
        $this->assertNotNull($seededEntry);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'organization_id' => $organization->id,
            'status' => 'draft',
            'verification_status' => 'not_submitted',
        ]);

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'organization_id' => $organization->id,
            'company_id' => $company->id,
        ]);

        $this->assertDatabaseHas('content_types', [
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'slug' => 'page',
        ]);

        $this->assertDatabaseHas('content_entries', [
            'id' => $seededEntry->id,
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'status' => 'draft',
            'visibility' => 'public',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'actor_user_id' => $user->id,
            'action' => 'onboarding.cms_page.seeded',
        ]);
    }
}
