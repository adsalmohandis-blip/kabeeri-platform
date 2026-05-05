<?php

namespace Database\Factories;

use App\Models\ImportBatch;
use App\Models\ImportJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImportBatch>
 */
class ImportBatchFactory extends Factory
{
    protected $model = ImportBatch::class;

    public function definition(): array
    {
        return [
            'import_job_id' => ImportJob::factory(),
            'batch_type' => fake()->randomElement(['posts', 'pages', 'media']),
            'status' => 'pending',
            'total_records' => 0,
            'processed_records' => 0,
            'failed_records' => 0,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
