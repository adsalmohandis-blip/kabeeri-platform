<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    protected $model = ServiceRequest::class;

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
            'company_id' => $contact->company_id,
            'site_id' => $contact->site_id,
            'contact_id' => $contact->id,
            'lead_id' => null,
            'request_number' => 'SR-'.fake()->unique()->numberBetween(1000, 9999),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'request_type' => 'general',
            'status' => 'new',
            'priority' => 'normal',
            'assigned_to' => fake()->boolean(30) ? User::factory() : null,
            'due_at' => null,
            'closed_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
