<?php

namespace Database\Factories;

use App\Models\CloudHealthCheck;
use App\Models\CloudSite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CloudHealthCheck>
 */
class CloudHealthCheckFactory extends Factory
{
    protected $model = CloudHealthCheck::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cloudSite = CloudSite::factory()->create();

        return [
            'organization_id' => $cloudSite->organization_id,
            'site_id' => $cloudSite->site_id,
            'cloud_site_id' => $cloudSite->id,
            'check_type' => 'manual',
            'status' => 'unknown',
            'response_time_ms' => null,
            'status_code' => null,
            'checked_at' => null,
            'message' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
