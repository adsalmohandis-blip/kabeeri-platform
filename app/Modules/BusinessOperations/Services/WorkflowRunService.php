<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowRun;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class WorkflowRunService
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function start(WorkflowDefinition $definition, Model $subject, array $context = []): WorkflowRun
    {
        if ($definition->status !== 'active') {
            throw ValidationException::withMessages([
                'workflow_definition_id' => 'Only active workflow definitions can start runs.',
            ]);
        }

        return WorkflowRun::query()->create([
            'organization_id' => $definition->organization_id,
            'workflow_definition_id' => $definition->id,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'status' => 'running',
            'current_step' => 0,
            'context' => $context,
            'started_at' => now(),
        ]);
    }

    public function complete(WorkflowRun $run): WorkflowRun
    {
        $run->forceFill([
            'status' => 'completed',
            'completed_at' => now(),
        ])->save();

        return $run->refresh();
    }
}
