<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\Organization;
use App\Models\Position;
use Illuminate\Validation\ValidationException;

class DepartmentPositionService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDepartment(Organization $organization, array $attributes): Department
    {
        return Department::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createPosition(Department $department, array $attributes): Position
    {
        return Position::query()->create([
            ...$attributes,
            'organization_id' => $department->organization_id,
            'department_id' => $department->id,
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    public function assign(EmployeeProfile $employee, Department $department, Position $position): EmployeeProfile
    {
        if (
            (int) $department->organization_id !== (int) $employee->organization_id
            || (int) $position->organization_id !== (int) $employee->organization_id
            || (int) $position->department_id !== (int) $department->id
        ) {
            throw ValidationException::withMessages([
                'position_id' => 'Department and position must belong to the employee organization.',
            ]);
        }

        $employee->forceFill([
            'department_id' => $department->id,
            'position_id' => $position->id,
        ])->save();

        return $employee->refresh();
    }
}
