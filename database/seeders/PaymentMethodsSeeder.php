<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['key' => 'cod', 'name' => 'Cash on Delivery'],
            ['key' => 'bank_transfer', 'name' => 'Bank Transfer'],
        ] as $method) {
            PaymentMethod::query()->updateOrCreate(
                ['key' => $method['key']],
                $method + [
                    'method_type' => 'manual',
                    'status' => 'active',
                    'settings' => [],
                ],
            );
        }
    }
}
