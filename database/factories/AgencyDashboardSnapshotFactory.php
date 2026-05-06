<?php

namespace Database\Factories;

use App\Models\AgencyDashboardSnapshot;
use App\Models\AgencyPartnerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgencyDashboardSnapshot>
 */
class AgencyDashboardSnapshotFactory extends Factory
{
    protected $model = AgencyDashboardSnapshot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $profile = AgencyPartnerProfile::factory()->create();

        return [
            'organization_id' => $profile->organization_id,
            'agency_partner_profile_id' => $profile->id,
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'metrics' => ['status' => $profile->status],
            'alerts' => [],
            'generated_at' => now(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
