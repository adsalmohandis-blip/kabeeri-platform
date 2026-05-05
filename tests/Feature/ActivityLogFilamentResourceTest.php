<?php

namespace Tests\Feature;

use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogFilamentResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_own_organization_activity_logs(): void
    {
        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $company = Company::factory()->create(['organization_id' => $organization->id]);
        $site = Site::factory()->create([
            'organization_id' => $organization->id,
            'company_id' => $company->id,
        ]);

        $log = app(ActivityLogger::class)->log(
            action: 'content_entry.published',
            organizationId: $organization->id,
            companyId: $company->id,
            siteId: $site->id,
            actorUserId: $owner->id,
            description: 'Published a page',
            subject: $site,
            metadata: ['source' => 'test'],
        );

        $this->actingAs($owner);

        $this->get('/admin/activity-logs')
            ->assertOk()
            ->assertSee('content_entry.published');

        $this->get(route('filament.admin.resources.activity-logs.view', ['record' => $log]))
            ->assertOk();

        $this->assertSame(1, ActivityLogResource::getEloquentQuery()->count());
    }

    public function test_user_cannot_view_logs_from_other_organization(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $log = app(ActivityLogger::class)->log(
            action: 'organization.updated',
            organizationId: $organization->id,
            siteId: $site->id,
            actorUserId: $owner->id,
            subject: $organization,
        );

        $this->actingAs($outsider);

        $response = $this->get(route('filament.admin.resources.activity-logs.view', ['record' => $log]));
        $this->assertContains($response->getStatusCode(), [403, 404]);

        $this->assertSame(0, ActivityLogResource::getEloquentQuery()->count());
    }
}
