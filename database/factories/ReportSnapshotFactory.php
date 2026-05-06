<?php

namespace Database\Factories;

use App\Models\ReportDefinition;
use App\Models\ReportSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportSnapshot>
 */
class ReportSnapshotFactory extends Factory
{
    protected $model = ReportSnapshot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $definition = ReportDefinition::factory()->create();

        return [
            'organization_id' => $definition->organization_id,
            'report_definition_id' => $definition->id,
            'status' => 'ready',
            'parameters' => [],
            'data' => ['rows' => [], 'totals' => []],
            'generated_at' => now(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
