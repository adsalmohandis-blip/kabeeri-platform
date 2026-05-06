<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\BusinessProject;
use App\Models\BusinessTask;
use App\Models\EmployeeProfile;
use Illuminate\Validation\ValidationException;

class BusinessProjectService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function addTask(BusinessProject $project, array $attributes): BusinessTask
    {
        if (($attributes['assigned_employee_profile_id'] ?? null) !== null) {
            $employee = EmployeeProfile::query()->findOrFail($attributes['assigned_employee_profile_id']);

            if ((int) $employee->organization_id !== (int) $project->organization_id) {
                throw ValidationException::withMessages([
                    'assigned_employee_profile_id' => 'Assignee must belong to the project organization.',
                ]);
            }
        }

        return $project->tasks()->create([
            ...$attributes,
            'organization_id' => $project->organization_id,
            'status' => $attributes['status'] ?? 'todo',
            'priority' => $attributes['priority'] ?? 'normal',
        ]);
    }

    public function completeTask(BusinessTask $task): BusinessTask
    {
        $task->forceFill([
            'status' => 'done',
            'completed_at' => now(),
        ])->save();

        return $task->refresh();
    }
}
