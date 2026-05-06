<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntryLine>
 */
class JournalEntryLineFactory extends Factory
{
    protected $model = JournalEntryLine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $entry = JournalEntry::factory()->create();

        return [
            'journal_entry_id' => $entry->id,
            'account_id' => Account::factory()->create(['organization_id' => $entry->organization_id])->id,
            'description' => fake()->optional()->sentence(),
            'debit' => 0,
            'credit' => 0,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
