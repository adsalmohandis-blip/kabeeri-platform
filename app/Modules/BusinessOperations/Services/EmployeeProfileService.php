<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\EmployeeProfile;
use App\Models\Organization;

class EmployeeProfileService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForOrganization(Organization $organization, array $attributes): EmployeeProfile
    {
        return EmployeeProfile::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'employee_number' => $attributes['employee_number'] ?? $this->nextEmployeeNumber($organization),
            'status' => $attributes['status'] ?? 'active',
            'employment_type' => $attributes['employment_type'] ?? 'full_time',
        ]);
    }

    public function terminate(EmployeeProfile $employee, ?string $date = null): EmployeeProfile
    {
        $employee->forceFill([
            'status' => 'terminated',
            'termination_date' => $date ?? today(),
        ])->save();

        return $employee->refresh();
    }

    protected function nextEmployeeNumber(Organization $organization): string
    {
        $next = EmployeeProfile::query()
            ->where('organization_id', $organization->id)
            ->count() + 1;

        return 'EMP-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
