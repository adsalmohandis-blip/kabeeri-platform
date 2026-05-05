<?php

namespace Database\Factories;

use App\Models\FormSubmission;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_submission_id' => FormSubmission::factory(),
            'organization_id' => fn (array $attributes): int => FormSubmission::query()->findOrFail($attributes['form_submission_id'])->organization_id,
            'site_id' => fn (array $attributes): ?int => FormSubmission::query()->findOrFail($attributes['form_submission_id'])->site_id,
            'company_id' => null,
            'contact_id' => null,
            'title' => fake()->optional()->sentence(3),
            'company_name' => fake()->optional()->company(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'source' => 'form',
            'lead_source_id' => null,
            'status' => 'new',
            'priority' => 'normal',
            'expected_value' => fake()->optional()->randomFloat(2, 100, 5000),
            'currency_code' => 'EGP',
            'score' => null,
            'score_breakdown' => null,
            'scored_at' => null,
            'assigned_to' => fake()->boolean(20) ? User::factory() : null,
            'qualified_at' => null,
            'converted_at' => null,
            'lost_at' => null,
            'lost_reason' => null,
            'message' => fake()->optional()->sentence(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
