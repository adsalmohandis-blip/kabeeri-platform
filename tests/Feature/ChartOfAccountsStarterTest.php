<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Organization;
use App\Modules\BusinessOperations\Services\ChartOfAccountsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartOfAccountsStarterTest extends TestCase
{
    use RefreshDatabase;

    public function test_starter_accounts_are_seeded_idempotently(): void
    {
        $organization = Organization::factory()->create();
        $service = app(ChartOfAccountsService::class);

        $service->seedStarterAccounts($organization);
        $service->seedStarterAccounts($organization);

        $this->assertSame(7, Account::query()->where('organization_id', $organization->id)->count());
        $this->assertDatabaseHas('accounts', [
            'organization_id' => $organization->id,
            'code' => '1000',
            'name' => 'Cash',
            'is_system' => true,
        ]);
    }

    public function test_account_can_have_parent_child_relationship(): void
    {
        $parent = Account::factory()->create(['code' => '1000']);
        $child = Account::factory()->create([
            'organization_id' => $parent->organization_id,
            'parent_id' => $parent->id,
            'code' => '1010',
        ]);

        $this->assertTrue($child->parent->is($parent));
        $this->assertTrue($parent->children->first()->is($child));
    }
}
