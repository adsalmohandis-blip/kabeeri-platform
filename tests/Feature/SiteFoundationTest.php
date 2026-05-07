<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class SiteFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_can_have_many_apps_sites(): void
    {
        $organization = Organization::factory()->create();

        Site::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'app-main',
        ]);

        Site::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'app-blog',
        ]);

        $this->assertCount(2, $organization->sites);
    }

    public function test_company_can_have_many_apps_sites(): void
    {
        $company = Company::factory()->create();

        Site::factory()->forCompany($company)->create(['slug' => 'company-app-1']);
        Site::factory()->forCompany($company)->create(['slug' => 'company-app-2']);

        $this->assertCount(2, $company->sites);
    }

    public function test_site_username_permalink_is_unique_across_platform(): void
    {
        $organizationA = Organization::factory()->create();
        $organizationB = Organization::factory()->create();

        Site::factory()->create([
            'organization_id' => $organizationA->id,
            'slug' => 'primary-app',
        ]);

        $this->expectException(QueryException::class);

        Site::factory()->create([
            'organization_id' => $organizationB->id,
            'slug' => 'primary-app',
        ]);
    }

    public function test_site_username_permalink_cannot_change_after_creation(): void
    {
        $site = Site::factory()->create(['slug' => 'fixed-app']);

        $this->expectException(LogicException::class);

        $site->forceFill(['slug' => 'changed-app'])->save();
    }
}
