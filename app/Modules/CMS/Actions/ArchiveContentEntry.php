<?php

namespace App\Modules\CMS\Actions;

use App\Models\ContentEntry;
use App\Models\User;
use App\Modules\Core\Services\ActivityLogger;
use App\Modules\Core\Services\PermissionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class ArchiveContentEntry
{
    public function __construct(
        protected PermissionService $permissionService,
        protected ActivityLogger $activityLogger,
    ) {}

    /**
     * @throws AuthorizationException
     */
    public function __invoke(User $actor, ContentEntry $entry): ContentEntry
    {
        if (
            ! $this->permissionService->hasSitePermission($actor, $entry->site_id, 'content.delete')
            && $entry->organization->owner_user_id !== $actor->id
        ) {
            throw new AuthorizationException('You are not allowed to archive this content entry.');
        }

        return DB::transaction(function () use ($actor, $entry): ContentEntry {
            $entry->forceFill([
                'status' => 'archived',
            ])->save();

            $this->activityLogger->log(
                action: 'content_entry.archived',
                organizationId: $entry->organization_id,
                siteId: $entry->site_id,
                actorUserId: $actor->id,
                description: 'Content entry archived',
                subject: $entry,
            );

            return $entry->refresh();
        });
    }
}
