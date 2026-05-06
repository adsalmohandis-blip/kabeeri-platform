<?php

namespace Database\Factories;

use App\Models\CloudDomain;
use App\Models\CloudSite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CloudDomain>
 */
class CloudDomainFactory extends Factory
{
    protected $model = CloudDomain::class;

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
            'domain' => fake()->unique()->domainName(),
            'domain_type' => 'custom',
            'verification_status' => 'pending',
            'dns_status' => 'unknown',
            'ssl_status' => 'not_requested',
            'is_primary' => false,
            'verified_at' => null,
            'last_checked_at' => null,
            'dns_records' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
