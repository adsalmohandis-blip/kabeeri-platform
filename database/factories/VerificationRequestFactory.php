<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Organization;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VerificationRequest>
 */
class VerificationRequestFactory extends Factory
{
    protected $model = VerificationRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'company_id' => Company::factory()->state(fn (array $attributes): array => [
                'organization_id' => $attributes['organization_id'],
            ]),
            'requested_by' => fake()->boolean(70) ? User::factory() : null,
            'status' => 'draft',
            'submitted_at' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
            'notes' => fake()->optional()->sentence(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
