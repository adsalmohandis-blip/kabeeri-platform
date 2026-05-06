<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\Organization;
use App\Models\Position;
use App\Modules\BusinessOperations\Services\DepartmentPositionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DepartmentsAndPositionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_position_and_employee_assignment(): void
    {
        $organization = Organization::factory()->create();
        $service = app(DepartmentPositionService::class);
        $department = $service->createDepartment($organization, ['name' => 'Sales', 'slug' => 'sales']);
        $position = $service->createPosition($department, ['title' => 'Sales Manager', 'slug' => 'sales-manager']);
        $employee = EmployeeProfile::factory()->create(['organization_id' => $organization->id]);

        $assigned = $service->assign($employee, $department, $position);

        $this->assertTrue($assigned->department->is($department));
        $this->assertTrue($assigned->position->is($position));
        $this->assertTrue($department->positions->first()->is($position));
    }

    public function test_employee_cannot_be_assigned_to_other_organization_department(): void
    {
        $this->expectException(ValidationException::class);

        app(DepartmentPositionService::class)->assign(
            EmployeeProfile::factory()->create(),
            Department::factory()->create(),
            Position::factory()->forDepartment()->create()
        );
    }
}
