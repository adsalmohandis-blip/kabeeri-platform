<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Organization;
use App\Models\WorkflowDefinition;
use Illuminate\Validation\ValidationException;

class WorkflowDefinitionService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForOrganization(Organization $organization, array $attributes): WorkflowDefinition
    {
        $this->validateSteps($attributes['steps'] ?? []);

        return WorkflowDefinition::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'status' => $attributes['status'] ?? 'draft',
        ]);
    }

    public function activate(WorkflowDefinition $definition): WorkflowDefinition
    {
        $this->validateSteps($definition->steps ?? []);

        $definition->forceFill(['status' => 'active'])->save();

        return $definition->refresh();
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     */
    protected function validateSteps(array $steps): void
    {
        foreach ($steps as $index => $step) {
            if (! isset($step['type'], $step['label'])) {
                throw ValidationException::withMessages([
                    "steps.{$index}" => 'Workflow steps require type and label.',
                ]);
            }
        }
    }
}
