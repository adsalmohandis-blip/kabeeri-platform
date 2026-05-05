<?php

namespace Database\Factories;

use App\Models\ImportJob;
use App\Models\RedirectSuggestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RedirectSuggestion>
 */
class RedirectSuggestionFactory extends Factory
{
    protected $model = RedirectSuggestion::class;

    public function definition(): array
    {
        return [
            'import_job_id' => ImportJob::factory(),
            'organization_id' => fn (array $attributes): int => ImportJob::query()->findOrFail($attributes['import_job_id'])->organization_id,
            'site_id' => fn (array $attributes): ?int => ImportJob::query()->findOrFail($attributes['import_job_id'])->site_id,
            'source_url' => '/'.fake()->slug(),
            'target_url' => '/'.fake()->slug(),
            'status' => 'pending',
            'reason' => 'Imported content slug changed.',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
