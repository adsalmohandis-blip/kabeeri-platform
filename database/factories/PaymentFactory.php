<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $invoice = Invoice::factory()->create();

        return [
            'organization_id' => $invoice->organization_id,
            'invoice_id' => $invoice->id,
            'payment_method_id' => null,
            'payment_number' => 'PAY-'.fake()->unique()->numberBetween(1000, 9999),
            'receipt_number' => 'RCPT-'.fake()->unique()->numberBetween(1000, 9999),
            'amount' => fake()->randomFloat(2, 100, 1000),
            'currency_code' => $invoice->currency_code,
            'status' => 'recorded',
            'paid_at' => now(),
            'reference' => fake()->optional()->bothify('REF-####'),
            'notes' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
