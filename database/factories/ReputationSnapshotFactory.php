<?php

namespace Database\Factories;

use App\Models\MallMirrorBusiness;
use App\Models\ReputationSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReputationSnapshot>
 */
class ReputationSnapshotFactory extends Factory
{
    protected $model = ReputationSnapshot::class;

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
            'subject_type' => $subject::class,
            'subject_id' => $subject->id,
            'review_count' => 0,
            'published_review_count' => 0,
            'average_rating' => null,
            'open_moderation_cases_count' => 0,
            'trust_score' => 0,
            'calculated_at' => now(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
