<?php

namespace Database\Factories;

use App\Models\AcademyBadge;
use App\Models\AcademyBadgeAward;
use App\Models\WorkNetworkProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademyBadgeAward>
 */
class AcademyBadgeAwardFactory extends Factory
{
    protected $model = AcademyBadgeAward::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $profile = WorkNetworkProfile::factory()->create();

        return [
            'organization_id' => $profile->organization_id,
            'academy_badge_id' => AcademyBadge::factory(),
            'user_id' => $profile->user_id,
            'work_network_profile_id' => $profile->id,
            'awarded_by_user_id' => null,
            'status' => 'active',
            'awarded_at' => now(),
            'expires_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
