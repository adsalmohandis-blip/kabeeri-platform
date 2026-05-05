<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quotation>
 */
class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

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
            'quotation_number' => 'QT-'.fake()->unique()->numberBetween(1000, 9999),
            'title' => fake()->optional()->sentence(4),
            'status' => 'draft',
            'currency_code' => 'EGP',
            'subtotal' => 0,
            'discount_total' => 0,
            'tax_total' => 0,
            'total' => 0,
            'valid_until' => now()->addDays(14),
            'issued_at' => null,
            'accepted_at' => null,
            'declined_at' => null,
            'decline_reason' => null,
            'notes' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
