<?php

namespace Database\Factories;

use App\Models\AcademyBadge;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AcademyBadge>
 */
class AcademyBadgeFactory extends Factory
{
    protected $model = AcademyBadge::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'key' => 'academy.'.Str::slug($name, '.').'.'.fake()->unique()->numberBetween(1000, 9999),
            'name' => Str::title($name),
            'badge_type' => 'skill',
            'status' => 'active',
            'description' => fake()->optional()->sentence(),
            'criteria' => ['manual_award' => true],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
