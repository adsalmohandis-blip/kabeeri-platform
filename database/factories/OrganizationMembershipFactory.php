<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizationMembership>
 */
class OrganizationMembershipFactory extends Factory
{
    protected $model = OrganizationMembership::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'membership_type' => fake()->randomElement([
                'owner',
                'admin',
                'employee',
                'contractor',
                'freelancer',
                'consultant',
                'external_collaborator',
                'legal_reviewer',
                'agency_member',
            ]),
            'status' => 'active',
            'job_title' => fake()->optional()->jobTitle(),
            'invited_by' => null,
            'invited_at' => null,
            'accepted_at' => now(),
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'visibility' => 'organization_only',
            'metadata' => ['source' => 'factory'],
        ];
    }
}
