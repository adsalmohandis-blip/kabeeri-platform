<?php

namespace App\Modules\Core\Services;

use App\Models\Notification;
use Carbon\CarbonInterface;

class NotificationService
{
    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function create(
        int $recipientUserId,
        string $type,
        string $title,
        ?string $body = null,
        ?string $actionUrl = null,
        ?int $organizationId = null,
        ?int $companyId = null,
        ?int $siteId = null,
        string $priority = 'normal',
        ?array $metadata = null,
    ): Notification {
        return Notification::query()->create([
            'recipient_user_id' => $recipientUserId,
            'organization_id' => $organizationId,
            'company_id' => $companyId,
            'site_id' => $siteId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'action_url' => $actionUrl,
            'priority' => $priority,
            'metadata' => $metadata,
        ]);
    }

    public function markAsRead(Notification $notification, ?CarbonInterface $readAt = null): Notification
    {
        $notification->forceFill([
            'read_at' => $readAt ?? now(),
        ])->save();

        return $notification->refresh();
    }
}
