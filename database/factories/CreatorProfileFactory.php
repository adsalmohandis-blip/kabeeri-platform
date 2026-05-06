<?php

namespace Database\Factories;

use App\Models\CreatorProfile;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CreatorProfile>
 */
class CreatorProfileFactory extends Factory
{
    protected $model = CreatorProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->name();

        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'user_id' => User::factory(),
            'display_name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'profile_type' => fake()->randomElement(['creator', 'publisher']),
            'status' => 'draft',
            'verification_status' => 'not_submitted',
            'bio' => fake()->optional()->paragraph(),
            'specialties' => ['themes'],
            'links' => ['website' => fake()->url()],
            'metadata' => ['source' => 'factory'],
            'submitted_at' => null,
            'approved_at' => null,
        ];
    }
}
