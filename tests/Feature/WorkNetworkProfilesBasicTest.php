<?php

namespace Tests\Feature;

use App\Models\EmployeeProfile;
use App\Models\Organization;
use App\Models\WorkNetworkProfile;
use App\Modules\Rabet\Services\WorkNetworkProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WorkNetworkProfilesBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_work_network_profile_can_be_created_from_employee_profile(): void
    {
        $employee = EmployeeProfile::factory()->create(['full_name' => 'Mona Builder']);

        $profile = app(WorkNetworkProfileService::class)->create($employee->organization, null, $employee, [
            'headline' => 'CMS Specialist',
            'skills' => ['cms', 'migration'],
        ]);

        $this->assertDatabaseHas('work_network_profiles', [
            'id' => $profile->id,
            'organization_id' => $employee->organization_id,
            'employee_profile_id' => $employee->id,
            'display_name' => 'Mona Builder',
            'visibility' => 'private',
            'status' => 'draft',
        ]);
        $this->assertSame(['cms', 'migration'], $profile->skills);
    }

    public function test_work_network_profile_can_be_published_and_archived(): void
    {
        $profile = WorkNetworkProfile::factory()->create(['status' => 'draft']);
        $service = app(WorkNetworkProfileService::class);

        $published = $service->publish($profile, 'available_for_projects');

        $this->assertSame('published', $published->status);
        $this->assertSame('public', $published->visibility);
        $this->assertSame('available_for_projects', $published->availability_status);
        $this->assertNotNull($published->published_at);

        $archived = $service->archive($published);

        $this->assertSame('archived', $archived->status);
        $this->assertSame('private', $archived->visibility);
    }

    public function test_work_network_profile_rejects_cross_tenant_employee(): void
    {
        $organization = Organization::factory()->create();
        $employee = EmployeeProfile::factory()->create();

        $this->expectException(ValidationException::class);

        app(WorkNetworkProfileService::class)->create($organization, null, $employee);
    }
}
