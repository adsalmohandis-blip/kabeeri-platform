<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyMembership>
 */
class CompanyMembershipFactory extends Factory
{
    protected $model = CompanyMembership::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'organization_membership_id' => null,
            'role_title' => fake()->optional()->jobTitle(),
            'department_id' => null,
            'manager_user_id' => null,
            'employment_type' => fake()->optional()->randomElement(['full_time', 'part_time', 'contract']),
            'status' => 'active',
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'public_work_history' => false,
            'metadata' => ['source' => 'factory'],
        ];
    }

    public function withOrganizationMembership(OrganizationMembership $membership): static
    {
        return $this->state(fn (): array => [
            'organization_membership_id' => $membership->id,
        ]);
    }
}
