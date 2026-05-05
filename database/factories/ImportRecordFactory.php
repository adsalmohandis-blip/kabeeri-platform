<?php

namespace Database\Factories;

use App\Models\ImportJob;
use App\Models\ImportRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImportRecord>
 */
class ImportRecordFactory extends Factory
{
    protected $model = ImportRecord::class;

    public function definition(): array
    {
        return [
            'import_job_id' => ImportJob::factory(),
            'source_entity_type' => fake()->randomElement(['post', 'page', 'attachment']),
            'source_entity_id' => (string) fake()->unique()->numberBetween(1, 99999),
            'target_type' => null,
            'target_id' => null,
            'status' => 'pending',
            'warnings' => null,
            'errors' => null,
            'source_payload' => ['title' => fake()->sentence()],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
