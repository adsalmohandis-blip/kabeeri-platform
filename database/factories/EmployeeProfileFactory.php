<?php

namespace Database\Factories;

use App\Models\EmployeeProfile;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeProfile>
 */
class EmployeeProfileFactory extends Factory
{
    protected $model = EmployeeProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'department_id' => null,
            'position_id' => null,
            'user_id' => null,
            'employee_number' => 'EMP-'.fake()->unique()->numberBetween(1000, 9999),
            'full_name' => fake()->name(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'employment_type' => 'full_time',
            'status' => 'active',
            'hire_date' => today(),
            'termination_date' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
