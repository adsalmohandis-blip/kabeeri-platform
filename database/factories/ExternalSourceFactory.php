<?php

namespace Database\Factories;

use App\Models\ExternalSource;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ExternalSource> */
class ExternalSourceFactory extends Factory
{
    protected $model = ExternalSource::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'site_id' => Site::factory()->state(fn (array $attributes): array => ['organization_id' => $attributes['organization_id']]),
            'source_type' => fake()->randomElement(['wordpress', 'woocommerce', 'shopify', 'custom_api', 'csv_feed', 'google_sheet', 'manual']),
            'source_name' => fake()->company(),
            'source_url' => fake()->optional()->url(),
            'connection_type' => 'manual',
            'status' => 'draft',
            'last_sync_at' => null,
            'settings' => ['notes' => 'No secrets stored.'],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
