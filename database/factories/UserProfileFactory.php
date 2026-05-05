<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProfile>
 */
class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => fake()->paragraph(),
            'country_id' => Country::factory(),
            'city' => fake()->city(),
            'website_url' => fake()->url(),
            'social_links' => [
                'linkedin' => 'https://linkedin.com/in/'.fake()->userName(),
                'x' => 'https://x.com/'.fake()->userName(),
            ],
            'visibility' => 'private',
            'metadata' => [
                'source' => 'factory',
            ],
        ];
    }
}
