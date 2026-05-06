<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use App\Modules\BusinessOperations\Services\EmployeeProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeProfilesBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_profile_can_be_created_for_organization(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $employee = app(EmployeeProfileService::class)->createForOrganization($organization, [
            'user_id' => $user->id,
            'full_name' => 'Nadia Employee',
            'email' => 'nadia@example.com',
        ]);

        $this->assertNotNull($employee->ulid);
        $this->assertSame('EMP-00001', $employee->employee_number);
        $this->assertTrue($employee->organization->is($organization));
        $this->assertTrue($employee->user->is($user));
        $this->assertSame('active', $employee->status);
    }

    public function test_employee_profile_can_be_terminated(): void
    {
        $employee = app(EmployeeProfileService::class)->createForOrganization(
            Organization::factory()->create(),
            ['full_name' => 'Nadia Employee']
        );

        $terminated = app(EmployeeProfileService::class)->terminate($employee, '2026-05-06');

        $this->assertSame('terminated', $terminated->status);
        $this->assertSame('2026-05-06', $terminated->termination_date->toDateString());
    }
}
