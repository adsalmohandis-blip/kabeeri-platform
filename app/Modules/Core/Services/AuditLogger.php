<?php

namespace App\Modules\Core\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    public function log(
        string $action,
        string $riskLevel,
        ?int $organizationId = null,
        ?int $companyId = null,
        ?int $siteId = null,
        ?int $actorUserId = null,
        ?Model $subject = null,
        ?array $before = null,
        ?array $after = null,
        ?string $reason = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): AuditLog {
        return AuditLog::query()->create([
            'organization_id' => $organizationId,
            'company_id' => $companyId,
            'site_id' => $siteId,
            'actor_user_id' => $actorUserId,
            'action' => $action,
            'risk_level' => $riskLevel,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'before' => $before,
            'after' => $after,
            'reason' => $reason,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    public function roleChanged(
        ?int $organizationId,
        ?int $actorUserId,
        ?Model $subject = null,
        ?array $before = null,
        ?array $after = null,
    ): AuditLog {
        return $this->log(
            action: 'role_changed',
            riskLevel: 'high',
            organizationId: $organizationId,
            actorUserId: $actorUserId,
            subject: $subject,
            before: $before,
            after: $after,
        );
    }

    public function permissionOverrideCreated(
        ?int $organizationId,
        ?int $actorUserId,
        ?Model $subject = null,
        ?string $reason = null,
    ): AuditLog {
        return $this->log(
            action: 'permission_override_created',
            riskLevel: 'high',
            organizationId: $organizationId,
            actorUserId: $actorUserId,
            subject: $subject,
            reason: $reason,
        );
    }

    public function exportRequested(
        ?int $organizationId,
        ?int $actorUserId,
        ?Model $subject = null,
        ?string $reason = null,
    ): AuditLog {
        return $this->log(
            action: 'export_requested',
            riskLevel: 'medium',
            organizationId: $organizationId,
            actorUserId: $actorUserId,
            subject: $subject,
            reason: $reason,
        );
    }
}
