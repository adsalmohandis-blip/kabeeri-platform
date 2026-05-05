<?php

namespace Database\Factories;

use App\Models\ImportJob;
use App\Models\MigrationReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MigrationReport>
 */
class MigrationReportFactory extends Factory
{
    protected $model = MigrationReport::class;

    public function definition(): array
    {
        return [
            'import_job_id' => ImportJob::factory(),
            'organization_id' => fn (array $attributes): int => ImportJob::query()->findOrFail($attributes['import_job_id'])->organization_id,
            'site_id' => fn (array $attributes): ?int => ImportJob::query()->findOrFail($attributes['import_job_id'])->site_id,
            'status' => 'generated',
            'data' => ['summary' => []],
        ];
    }
}
