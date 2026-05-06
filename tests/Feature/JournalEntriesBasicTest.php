<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Organization;
use App\Modules\BusinessOperations\Services\JournalEntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class JournalEntriesBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_balanced_journal_entry_can_be_posted(): void
    {
        $organization = Organization::factory()->create();
        $debit = Account::factory()->create(['organization_id' => $organization->id]);
        $credit = Account::factory()->create(['organization_id' => $organization->id]);
        $service = app(JournalEntryService::class);
        $entry = $service->createDraft($organization);

        $service->addLine($entry, $debit, debit: 100);
        $entry = $service->addLine($entry->refresh(), $credit, credit: 100);
        $posted = $service->post($entry);

        $this->assertSame('100.00', $posted->debit_total);
        $this->assertSame('100.00', $posted->credit_total);
        $this->assertSame('posted', $posted->status);
        $this->assertNotNull($posted->posted_at);
    }

    public function test_unbalanced_journal_entry_cannot_be_posted(): void
    {
        $organization = Organization::factory()->create();
        $account = Account::factory()->create(['organization_id' => $organization->id]);
        $service = app(JournalEntryService::class);
        $entry = $service->addLine($service->createDraft($organization), $account, debit: 100);

        $this->expectException(ValidationException::class);

        $service->post($entry);
    }

    public function test_journal_line_rejects_other_organization_account(): void
    {
        $this->expectException(ValidationException::class);

        app(JournalEntryService::class)->addLine(
            app(JournalEntryService::class)->createDraft(Organization::factory()->create()),
            Account::factory()->create(),
            debit: 100
        );
    }
}
