<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowRun;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class WorkflowHookService
{
    /**
     * @param  array<string, mixed>  $context
     * @return Collection<int, WorkflowRun>
     */
    public function dispatch(string $triggerType, Model $subject, array $context = []): Collection
    {
        $organizationId = $subject->organization_id ?? null;

        if ($organizationId === null) {
            return collect();
        }

        return WorkflowDefinition::query()
            ->where('organization_id', $organizationId)
            ->where('trigger_type', $triggerType)
            ->where('status', 'active')
            ->get()
            ->map(fn (WorkflowDefinition $definition) => app(WorkflowRunService::class)->start(
                $definition,
                $subject,
                ['trigger_type' => $triggerType, ...$context]
            ));
    }
}
