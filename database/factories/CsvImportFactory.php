<?php

namespace Database\Factories;

use App\Models\CsvImport;
use App\Models\ExternalSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CsvImport>
 */
class CsvImportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_source_id' => ExternalSource::factory()->state(['source_type' => 'csv_feed']),
            'file_path' => 'storage/csv_imports/sample.csv',
            'status' => 'pending',
            'total_rows' => 0,
            'imported_count' => 0,
            'skipped_count' => 0,
            'error_count' => 0,
            'errors' => null,
            'metadata' => ['source' => 'factory'],
            'started_at' => null,
            'completed_at' => null,
        ];
    }
}
