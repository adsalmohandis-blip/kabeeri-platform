<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_write_activity_log(): void
    {
        $actor = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $actor->id]);
        $company = Company::factory()->create(['organization_id' => $organization->id]);
        $site = Site::factory()->forCompany($company)->create([
            'organization_id' => $organization->id,
        ]);

        $log = app(ActivityLogger::class)->log(
            action: 'site.created',
            organizationId: $organization->id,
            companyId: $company->id,
            siteId: $site->id,
            actorUserId: $actor->id,
            description: 'App created',
            subject: $site,
            ipAddress: '127.0.0.1',
            userAgent: 'PHPUnit',
            metadata: ['source' => 'test'],
        );

        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'action' => 'site.created',
            'organization_id' => $organization->id,
            'company_id' => $company->id,
            'site_id' => $site->id,
            'actor_user_id' => $actor->id,
        ]);
    }

    public function test_can_query_activity_logs_by_organization(): void
    {
        $actor = User::factory()->create();
        $orgA = Organization::factory()->create(['owner_user_id' => $actor->id]);
        $orgB = Organization::factory()->create(['owner_user_id' => User::factory()->create()->id]);

        app(ActivityLogger::class)->log(
            action: 'organization.updated',
            organizationId: $orgA->id,
            actorUserId: $actor->id,
            subject: $orgA,
        );

        app(ActivityLogger::class)->log(
            action: 'organization.updated',
            organizationId: $orgB->id,
            actorUserId: $actor->id,
            subject: $orgB,
        );

        $this->assertCount(1, ActivityLog::query()->forOrganization($orgA->id)->get());
        $this->assertCount(1, ActivityLog::query()->forOrganization($orgB->id)->get());
    }
}
