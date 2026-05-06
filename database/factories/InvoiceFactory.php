<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

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
            'quotation_id' => null,
            'invoice_number' => 'INV-'.fake()->unique()->numberBetween(1000, 9999),
            'status' => 'draft',
            'payment_status' => 'unpaid',
            'currency_code' => 'EGP',
            'subtotal' => 0,
            'discount_total' => 0,
            'tax_total' => 0,
            'paid_total' => 0,
            'total' => 0,
            'issued_at' => null,
            'due_at' => now()->addDays(14),
            'notes' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
