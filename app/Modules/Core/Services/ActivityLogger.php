<?php

namespace App\Modules\Core\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function log(
        string $action,
        ?int $organizationId = null,
        ?int $companyId = null,
        ?int $siteId = null,
        ?int $actorUserId = null,
        ?string $description = null,
        ?Model $subject = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?array $metadata = null,
    ): ActivityLog {
        return ActivityLog::query()->create([
            'organization_id' => $organizationId,
            'company_id' => $companyId,
            'site_id' => $siteId,
            'actor_user_id' => $actorUserId,
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'metadata' => $metadata,
        ]);
    }
}
