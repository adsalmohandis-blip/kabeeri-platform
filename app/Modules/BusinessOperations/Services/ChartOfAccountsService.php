<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Account;
use App\Models\Organization;

class ChartOfAccountsService
{
    /**
     * @return array<int, Account>
     */
    public function seedStarterAccounts(Organization $organization): array
    {
        $definitions = [
            ['1000', 'Cash', 'asset', 'debit'],
            ['1100', 'Accounts Receivable', 'asset', 'debit'],
            ['2000', 'Accounts Payable', 'liability', 'credit'],
            ['3000', 'Owner Equity', 'equity', 'credit'],
            ['4000', 'Sales Revenue', 'revenue', 'credit'],
            ['5000', 'Cost of Goods Sold', 'expense', 'debit'],
            ['6000', 'Operating Expenses', 'expense', 'debit'],
        ];

        return collect($definitions)
            ->map(fn (array $definition): Account => Account::query()->firstOrCreate(
                [
                    'organization_id' => $organization->id,
                    'code' => $definition[0],
                ],
                [
                    'name' => $definition[1],
                    'account_type' => $definition[2],
                    'normal_balance' => $definition[3],
                    'status' => 'active',
                    'is_system' => true,
                ]
            ))
            ->all();
    }
}
