<?php

namespace App\Modules\CMS\Actions;

use App\Models\ContentEntry;
use App\Models\User;
use App\Modules\Core\Services\ActivityLogger;
use App\Modules\Core\Services\PermissionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PublishContentEntry
{
    public function __construct(
        protected PermissionService $permissionService,
        protected ActivityLogger $activityLogger,
    ) {}

    /**
     * @throws AuthorizationException
     */
    public function __invoke(User $actor, ContentEntry $entry, ?Carbon $publishedAt = null): ContentEntry
    {
        if (
            ! $this->permissionService->hasSitePermission($actor, $entry->site_id, 'content.publish')
            && $entry->organization->owner_user_id !== $actor->id
        ) {
            throw new AuthorizationException('You are not allowed to publish this content entry.');
        }

        return DB::transaction(function () use ($actor, $entry, $publishedAt): ContentEntry {
            $entry->forceFill([
                'status' => 'published',
                'published_at' => $publishedAt ?? now(),
            ])->save();

            $this->activityLogger->log(
                action: 'content_entry.published',
                organizationId: $entry->organization_id,
                siteId: $entry->site_id,
                actorUserId: $actor->id,
                description: 'Content entry published',
                subject: $entry,
            );

            return $entry->refresh();
        });
    }
}
