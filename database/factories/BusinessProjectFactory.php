<?php

namespace Database\Factories;

use App\Models\BusinessProject;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BusinessProject>
 */
class BusinessProjectFactory extends Factory
{
    protected $model = BusinessProject::class;

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
            'company_id' => null,
            'lead_id' => null,
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numberBetween(100, 999)),
            'status' => 'active',
            'starts_on' => today(),
            'ends_on' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
