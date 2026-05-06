<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\ReportDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportDefinition>
 */
class ReportDefinitionFactory extends Factory
{
    protected $model = ReportDefinition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'key' => 'report_'.fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->words(3, true),
            'report_type' => 'table',
            'status' => 'active',
            'query_config' => ['source' => 'manual'],
            'columns' => ['name', 'status'],
            'filters' => [],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
