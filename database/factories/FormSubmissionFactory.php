<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormSubmission>
 */
class FormSubmissionFactory extends Factory
{
    protected $model = FormSubmission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'organization_id' => fn (array $attributes): int => Form::query()->findOrFail($attributes['form_id'])->organization_id,
            'site_id' => fn (array $attributes): ?int => Form::query()->findOrFail($attributes['form_id'])->site_id,
            'submitted_by_user_id' => fake()->boolean(30) ? User::factory() : null,
            'data' => ['message' => fake()->sentence()],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'status' => 'new',
            'source_url' => fake()->optional()->url(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
