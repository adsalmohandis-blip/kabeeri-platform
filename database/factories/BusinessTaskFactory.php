<?php

namespace Database\Factories;

use App\Models\BusinessProject;
use App\Models\BusinessTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessTask>
 */
class BusinessTaskFactory extends Factory
{
    protected $model = BusinessTask::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $project = BusinessProject::factory()->create();

        return [
            'organization_id' => $project->organization_id,
            'business_project_id' => $project->id,
            'assigned_employee_profile_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => 'todo',
            'priority' => 'normal',
            'due_on' => null,
            'completed_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
