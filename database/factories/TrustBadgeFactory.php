<?php

namespace Database\Factories;

use App\Models\TrustBadge;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TrustBadge>
 */
class TrustBadgeFactory extends Factory
{
    protected $model = TrustBadge::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'key' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'name' => Str::title($name),
            'badge_type' => 'verification',
            'status' => 'active',
            'description' => fake()->optional()->sentence(),
            'criteria' => ['manual_review' => true],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
