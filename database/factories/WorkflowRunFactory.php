<?php

namespace Database\Factories;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowRun>
 */
class WorkflowRunFactory extends Factory
{
    protected $model = WorkflowRun::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $definition = WorkflowDefinition::factory()->create(['status' => 'active']);

        return [
            'organization_id' => $definition->organization_id,
            'workflow_definition_id' => $definition->id,
            'subject_type' => 'manual',
            'subject_id' => fake()->numberBetween(1, 999),
            'status' => 'running',
            'current_step' => 0,
            'context' => [],
            'started_at' => now(),
            'completed_at' => null,
            'failed_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
