<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CreatorProfile;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Core\Services\CreatorProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreatorPublisherProfilesBetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_profile_can_be_created_for_user(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['name' => 'Nour Creator']);

        $profile = app(CreatorProfileService::class)->create($organization, $user, null, [
            'profile_type' => 'creator',
            'specialties' => ['themes', 'copywriting'],
            'links' => ['website' => 'https://creator.example'],
        ]);

        $this->assertDatabaseHas('creator_profiles', [
            'id' => $profile->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'display_name' => 'Nour Creator',
            'profile_type' => 'creator',
            'status' => 'draft',
        ]);
        $this->assertSame(['themes', 'copywriting'], $profile->specialties);
    }

    public function test_publisher_profile_can_be_submitted_and_approved(): void
    {
        $profile = CreatorProfile::factory()->create([
            'profile_type' => 'publisher',
            'status' => 'draft',
        ]);
        $service = app(CreatorProfileService::class);

        $submitted = $service->submit($profile);

        $this->assertSame('submitted', $submitted->status);
        $this->assertNotNull($submitted->submitted_at);

        $approved = $service->approve($submitted);

        $this->assertSame('active', $approved->status);
        $this->assertNotNull($approved->approved_at);
    }

    public function test_profile_approval_requires_submission(): void
    {
        $profile = CreatorProfile::factory()->create(['status' => 'draft']);

        $this->expectException(ValidationException::class);

        app(CreatorProfileService::class)->approve($profile);
    }

    public function test_profile_rejects_cross_tenant_company(): void
    {
        $organization = Organization::factory()->create();
        $company = Company::factory()->create();

        $this->expectException(ValidationException::class);

        app(CreatorProfileService::class)->create($organization, null, $company);
    }
}
