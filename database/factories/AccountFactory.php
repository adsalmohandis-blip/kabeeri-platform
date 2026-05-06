<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'parent_id' => null,
            'code' => (string) fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->words(2, true),
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'status' => 'active',
            'is_system' => false,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
