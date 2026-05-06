<?php

namespace Database\Factories;

use App\Models\MallMirrorBusiness;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reviewable = MallMirrorBusiness::factory()->create();

        return [
            'organization_id' => $reviewable->organization_id,
            'site_id' => $reviewable->site_id,
            'reviewable_type' => $reviewable::class,
            'reviewable_id' => $reviewable->id,
            'reviewer_user_id' => null,
            'rating' => fake()->numberBetween(1, 5),
            'title' => fake()->optional()->sentence(4),
            'body' => fake()->optional()->paragraph(),
            'source' => 'manual',
            'status' => 'pending',
            'submitted_at' => now(),
            'published_at' => null,
            'rejected_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
