<?php

namespace Database\Factories;

use App\Models\CloudSite;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CloudSite>
 */
class CloudSiteFactory extends Factory
{
    protected $model = CloudSite::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $site = Site::factory()->create();

        return [
            'organization_id' => $site->organization_id,
            'site_id' => $site->id,
            'environment' => 'production',
            'provider' => null,
            'region' => null,
            'deployment_status' => 'draft',
            'health_status' => 'unknown',
            'public_url' => null,
            'last_deployed_at' => null,
            'last_checked_at' => null,
            'settings' => ['source' => 'factory'],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
