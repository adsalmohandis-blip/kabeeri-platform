<?php

namespace Database\Factories;

use App\Models\MallMirrorBusiness;
use App\Models\TrustBadge;
use App\Models\TrustBadgeAward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrustBadgeAward>
 */
class TrustBadgeAwardFactory extends Factory
{
    protected $model = TrustBadgeAward::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subject = MallMirrorBusiness::factory()->create();

        return [
            'organization_id' => $subject->organization_id,
            'site_id' => $subject->site_id,
            'trust_badge_id' => TrustBadge::factory(),
            'subject_type' => $subject::class,
            'subject_id' => $subject->id,
            'awarded_by_user_id' => null,
            'status' => 'active',
            'awarded_at' => now(),
            'expires_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
