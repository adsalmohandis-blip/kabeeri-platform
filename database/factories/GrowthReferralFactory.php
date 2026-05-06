<?php

namespace Database\Factories;

use App\Models\GrowthReferral;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GrowthReferral>
 */
class GrowthReferralFactory extends Factory
{
    protected $model = GrowthReferral::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'referrer_user_id' => User::factory(),
            'referred_organization_id' => null,
            'code' => Str::upper(fake()->unique()->bothify('REF-####??')),
            'referred_email' => fake()->optional()->safeEmail(),
            'source' => 'manual',
            'status' => 'pending',
            'accepted_at' => null,
            'expired_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
