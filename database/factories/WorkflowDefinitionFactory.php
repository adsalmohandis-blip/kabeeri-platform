<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\WorkflowDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WorkflowDefinition>
 */
class WorkflowDefinitionFactory extends Factory
{
    protected $model = WorkflowDefinition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'organization_id' => Organization::factory(),
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numberBetween(100, 999)),
            'trigger_type' => 'manual',
            'status' => 'draft',
            'steps' => [
                ['type' => 'approval', 'label' => 'Manager review'],
            ],
            'conditions' => [],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
