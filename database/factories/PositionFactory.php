<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Organization;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->jobTitle();

        return [
            'organization_id' => Organization::factory(),
            'department_id' => null,
            'title' => $title,
            'slug' => Str::slug($title.'-'.fake()->unique()->numberBetween(100, 999)),
            'level' => fake()->optional()->randomElement(['junior', 'mid', 'senior', 'lead']),
            'status' => 'active',
            'metadata' => ['source' => 'factory'],
        ];
    }

    public function forDepartment(?Department $department = null): self
    {
        return $this->state(function () use ($department): array {
            $department ??= Department::factory()->create();

            return [
                'organization_id' => $department->organization_id,
                'department_id' => $department->id,
            ];
        });
    }
}
