<?php

namespace Database\Factories;

use App\Models\ImportJob;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImportJob>
 */
class ImportJobFactory extends Factory
{
    protected $model = ImportJob::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'site_id' => Site::factory()->state(fn (array $attributes): array => [
                'organization_id' => $attributes['organization_id'],
            ]),
            'source_type' => 'wordpress',
            'source_name' => fake()->optional()->domainName(),
            'status' => 'draft',
            'file_media_id' => null,
            'started_by' => fake()->boolean(40) ? User::factory() : null,
            'started_at' => null,
            'completed_at' => null,
            'failed_at' => null,
            'summary' => null,
            'settings' => ['allow_publish' => false],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
