<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'entry_number' => 'JE-'.fake()->unique()->numberBetween(1000, 9999),
            'status' => 'draft',
            'entry_date' => today(),
            'source_type' => null,
            'source_id' => null,
            'memo' => fake()->optional()->sentence(),
            'debit_total' => 0,
            'credit_total' => 0,
            'posted_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
