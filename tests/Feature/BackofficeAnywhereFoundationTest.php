<?php

namespace Tests\Feature;

use App\Models\BackofficeWorkspace;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Site;
use App\Modules\Platform\Services\BackofficeWorkspaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BackofficeAnywhereFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_backoffice_workspace_can_be_created_for_site_context(): void
    {
        $company = Company::factory()->create();
        $site = Site::factory()->forCompany($company)->create();

        $workspace = app(BackofficeWorkspaceService::class)->create(
            $company->organization,
            $company,
            $site,
            [
                'name' => 'Operations Hub',
                'slug' => 'operations-hub',
                'enabled_modules' => ['crm', 'reports'],
                'navigation' => [['label' => 'Reports', 'route' => 'reports.index']],
            ],
        );

        $this->assertDatabaseHas('backoffice_workspaces', [
            'id' => $workspace->id,
            'organization_id' => $company->organization_id,
            'company_id' => $company->id,
            'site_id' => $site->id,
            'status' => 'draft',
            'access_scope' => 'site',
        ]);
        $this->assertSame(['crm', 'reports'], $workspace->enabled_modules);
    }

    public function test_workspace_can_be_activated_and_archived_without_public_exposure(): void
    {
        $workspace = BackofficeWorkspace::factory()->create(['status' => 'draft']);

        $service = app(BackofficeWorkspaceService::class);

        $this->assertSame('active', $service->activate($workspace)->status);
        $this->assertSame('archived', $service->archive($workspace)->status);
    }

    public function test_workspace_rejects_cross_tenant_site(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create();

        $this->expectException(ValidationException::class);

        app(BackofficeWorkspaceService::class)->create($organization, null, $site, [
            'name' => 'Wrong Tenant',
        ]);
    }

    public function test_workspace_rejects_site_from_different_company(): void
    {
        $company = Company::factory()->create();
        $otherCompany = Company::factory()->create(['organization_id' => $company->organization_id]);
        $site = Site::factory()->forCompany($otherCompany)->create();

        $this->expectException(ValidationException::class);

        app(BackofficeWorkspaceService::class)->create($company->organization, $company, $site, [
            'name' => 'Wrong Company',
        ]);
    }
}
