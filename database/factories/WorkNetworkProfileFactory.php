<?php

namespace Database\Factories;

use App\Models\EmployeeProfile;
use App\Models\WorkNetworkProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WorkNetworkProfile>
 */
class WorkNetworkProfileFactory extends Factory
{
    protected $model = WorkNetworkProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = EmployeeProfile::factory()->create();

        return [
            'organization_id' => $employee->organization_id,
            'user_id' => $employee->user_id,
            'employee_profile_id' => $employee->id,
            'display_name' => $employee->full_name,
            'slug' => Str::slug($employee->full_name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'headline' => fake()->optional()->jobTitle(),
            'bio' => fake()->optional()->paragraph(),
            'skills' => ['cms', 'support'],
            'links' => [],
            'visibility' => 'private',
            'status' => 'draft',
            'availability_status' => 'not_listed',
            'published_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
