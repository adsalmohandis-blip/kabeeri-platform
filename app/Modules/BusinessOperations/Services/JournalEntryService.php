<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Organization;
use Illuminate\Validation\ValidationException;

class JournalEntryService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraft(Organization $organization, array $attributes = []): JournalEntry
    {
        return JournalEntry::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'entry_number' => $attributes['entry_number'] ?? $this->nextEntryNumber($organization),
            'status' => 'draft',
            'entry_date' => $attributes['entry_date'] ?? today(),
        ]);
    }

    public function addLine(JournalEntry $entry, Account $account, float $debit = 0, float $credit = 0): JournalEntry
    {
        if ((int) $account->organization_id !== (int) $entry->organization_id) {
            throw ValidationException::withMessages([
                'account_id' => 'The selected account does not belong to this journal entry organization.',
            ]);
        }

        if (($debit <= 0 && $credit <= 0) || ($debit > 0 && $credit > 0)) {
            throw ValidationException::withMessages([
                'amount' => 'A journal line must have either debit or credit amount.',
            ]);
        }

        $entry->lines()->create([
            'account_id' => $account->id,
            'debit' => $debit,
            'credit' => $credit,
        ]);

        return $this->recalculateTotals($entry);
    }

    public function post(JournalEntry $entry): JournalEntry
    {
        $entry = $this->recalculateTotals($entry);

        if ((float) $entry->debit_total !== (float) $entry->credit_total || (float) $entry->debit_total <= 0) {
            throw ValidationException::withMessages([
                'balance' => 'Journal entry debits and credits must balance before posting.',
            ]);
        }

        $entry->forceFill([
            'status' => 'posted',
            'posted_at' => now(),
        ])->save();

        return $entry->refresh();
    }

    public function recalculateTotals(JournalEntry $entry): JournalEntry
    {
        $lines = $entry->lines()->get();

        $entry->forceFill([
            'debit_total' => $lines->sum(fn ($line): float => (float) $line->debit),
            'credit_total' => $lines->sum(fn ($line): float => (float) $line->credit),
        ])->save();

        return $entry->refresh();
    }

    protected function nextEntryNumber(Organization $organization): string
    {
        $next = JournalEntry::query()
            ->where('organization_id', $organization->id)
            ->count() + 1;

        return 'JE-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
