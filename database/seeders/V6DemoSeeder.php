<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Modules\Platform\Services\V6EnterpriseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class V6DemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->where('slug', 'kabeeri-demo-org')->first()
            ?? Organization::query()->first();

        if (! $organization) {
            return;
        }

        if (DB::table('enterprise_identity_providers')
            ->where('organization_id', $organization->id)
            ->where('name', 'V6 Demo SSO')
            ->exists()) {
            return;
        }

        app(V6EnterpriseService::class)->seedFoundationRecords($organization);
    }
}
