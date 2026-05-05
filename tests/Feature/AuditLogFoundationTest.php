<?php

namespace Tests\Feature;

use App\Models\MembershipPermissionOverride;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Permission;
use App\Models\User;
use App\Modules\Core\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_audit_log_with_before_and_after(): void
    {
        $actor = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $actor->id]);
        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $actor->id,
        ]);

        $log = app(AuditLogger::class)->log(
            action: 'membership.role_changed',
            riskLevel: 'high',
            organizationId: $organization->id,
            actorUserId: $actor->id,
            subject: $membership,
            before: ['role' => 'editor'],
            after: ['role' => 'organization-admin'],
            reason: 'Promotion',
            ipAddress: '127.0.0.1',
            userAgent: 'PHPUnit',
        );

        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'action' => 'membership.role_changed',
            'risk_level' => 'high',
            'organization_id' => $organization->id,
            'actor_user_id' => $actor->id,
            'reason' => 'Promotion',
        ]);

        $this->assertSame('editor', $log->before['role']);
        $this->assertSame('organization-admin', $log->after['role']);
    }

    public function test_audit_logger_example_methods_write_expected_actions(): void
    {
        $actor = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $actor->id]);
        $membership = OrganizationMembership::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $actor->id,
        ]);

        $override = MembershipPermissionOverride::query()->create([
            'membership_type' => $membership->getMorphClass(),
            'membership_id' => $membership->id,
            'permission_id' => Permission::query()->create([
                'name' => 'Manage Organization',
                'slug' => 'organization.manage.test',
                'group' => 'organization',
                'risk_level' => 'high',
            ])->id,
            'effect' => 'deny',
            'reason' => 'Test',
            'created_by' => $actor->id,
        ]);

        $logger = app(AuditLogger::class);

        $logger->roleChanged(
            organizationId: $organization->id,
            actorUserId: $actor->id,
            subject: $membership,
            before: ['role' => 'viewer'],
            after: ['role' => 'editor'],
        );

        $logger->permissionOverrideCreated(
            organizationId: $organization->id,
            actorUserId: $actor->id,
            subject: $override,
            reason: 'Security policy',
        );

        $logger->exportRequested(
            organizationId: $organization->id,
            actorUserId: $actor->id,
            subject: $organization,
            reason: 'Owner request',
        );

        $this->assertDatabaseHas('audit_logs', ['action' => 'role_changed', 'risk_level' => 'high']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'permission_override_created', 'risk_level' => 'high']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'export_requested', 'risk_level' => 'medium']);
    }
}
