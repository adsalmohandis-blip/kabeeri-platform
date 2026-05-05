<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\CrmActivity;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmActivity>
 */
class CrmActivityFactory extends Factory
{
    protected $model = CrmActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contact = Contact::factory()->create();

        return [
            'organization_id' => $contact->organization_id,
            'contact_id' => $contact->id,
            'lead_id' => null,
            'assigned_to' => fake()->boolean(30) ? User::factory() : null,
            'activity_type' => fake()->randomElement(['note', 'call', 'email', 'meeting', 'task']),
            'subject' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => 'open',
            'priority' => 'normal',
            'due_at' => fake()->optional()->dateTimeBetween('now', '+2 weeks'),
            'completed_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }

    public function forLead(?Lead $lead = null): self
    {
        return $this->state(function () use ($lead): array {
            $lead ??= Lead::factory()->create();

            return [
                'organization_id' => $lead->organization_id,
                'contact_id' => $lead->contact_id,
                'lead_id' => $lead->id,
            ];
        });
    }
}
