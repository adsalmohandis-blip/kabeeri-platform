<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentMethod;
use Database\Seeders\PaymentMethodsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodPlaceholdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_cod_and_bank_transfer_are_seeded_as_manual_methods(): void
    {
        $this->seed(PaymentMethodsSeeder::class);
        $this->seed(PaymentMethodsSeeder::class);

        $this->assertSame(2, PaymentMethod::query()->count());
        $this->assertDatabaseHas('payment_methods', ['key' => 'cod', 'method_type' => 'manual']);
        $this->assertDatabaseHas('payment_methods', ['key' => 'bank_transfer', 'method_type' => 'manual']);
    }

    public function test_order_can_reference_payment_method(): void
    {
        $this->seed(PaymentMethodsSeeder::class);
        $method = PaymentMethod::query()->where('key', 'cod')->firstOrFail();

        $order = Order::factory()->create(['payment_method_id' => $method->id]);

        $this->assertSame($method->id, $order->payment_method_id);
    }
}
