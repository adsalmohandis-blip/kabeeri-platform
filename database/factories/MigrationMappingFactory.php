<?php

namespace Database\Factories;

use App\Models\ImportJob;
use App\Models\MigrationMapping;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MigrationMapping>
 */
class MigrationMappingFactory extends Factory
{
    protected $model = MigrationMapping::class;

    public function definition(): array
    {
        return [
            'import_job_id' => ImportJob::factory(),
            'source_type' => 'wordpress_author',
            'source_id' => (string) fake()->unique()->numberBetween(1, 9999),
            'source_key' => fake()->userName(),
            'target_type' => null,
            'target_id' => null,
            'mapping_status' => 'pending',
            'mapping_strategy' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
