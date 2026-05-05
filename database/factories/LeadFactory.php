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
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'source' => 'form',
            'status' => 'new',
            'score' => null,
            'assigned_to' => fake()->boolean(20) ? User::factory() : null,
            'message' => fake()->optional()->sentence(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
