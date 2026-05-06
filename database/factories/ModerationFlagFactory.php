<?php

namespace Database\Factories;

use App\Models\MallMirrorBusiness;
use App\Models\ModerationFlag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModerationFlag>
 */
class ModerationFlagFactory extends Factory
{
    protected $model = ModerationFlag::class;

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
            'moderation_case_id' => null,
            'subject_type' => $subject::class,
            'subject_id' => $subject->id,
            'reported_by_user_id' => null,
            'flag_type' => 'content_report',
            'reason' => 'inaccurate',
            'severity' => 'normal',
            'status' => 'new',
            'reporter_contact' => null,
            'message' => fake()->optional()->sentence(),
            'evidence' => [['type' => 'url', 'value' => fake()->url()]],
            'metadata' => ['source' => 'factory'],
            'reviewed_at' => null,
        ];
    }
}
