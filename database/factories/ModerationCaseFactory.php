<?php

namespace Database\Factories;

use App\Models\MallMirrorBusiness;
use App\Models\ModerationCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModerationCase>
 */
class ModerationCaseFactory extends Factory
{
    protected $model = ModerationCase::class;

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
            'case_type' => 'content',
            'reason' => 'quality_review',
            'priority' => 'normal',
            'status' => 'open',
            'reported_by_user_id' => null,
            'assigned_to_user_id' => null,
            'opened_at' => now(),
            'resolved_at' => null,
            'closed_at' => null,
            'resolution' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
