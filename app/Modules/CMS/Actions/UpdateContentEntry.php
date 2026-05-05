<?php

namespace App\Modules\CMS\Actions;

use App\Models\ContentEntry;
use App\Models\User;
use App\Modules\Core\Services\ActivityLogger;
use App\Modules\Core\Services\PermissionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class UpdateContentEntry
{
    public function __construct(
        protected PermissionService $permissionService,
        protected ActivityLogger $activityLogger,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws AuthorizationException
     */
    public function __invoke(User $actor, ContentEntry $entry, array $attributes): ContentEntry
    {
        if (
            ! $this->permissionService->hasSitePermission($actor, $entry->site_id, 'content.edit')
            && $entry->organization->owner_user_id !== $actor->id
        ) {
            throw new AuthorizationException('You are not allowed to edit this content entry.');
        }

        return DB::transaction(function () use ($actor, $entry, $attributes): ContentEntry {
            $entry->fill([
                'title' => $attributes['title'] ?? $entry->title,
                'slug' => $attributes['slug'] ?? $entry->slug,
                'excerpt' => $attributes['excerpt'] ?? $entry->excerpt,
                'body' => $attributes['body'] ?? $entry->body,
                'status' => $attributes['status'] ?? $entry->status,
                'visibility' => $attributes['visibility'] ?? $entry->visibility,
                'seo' => $attributes['seo'] ?? $entry->seo,
                'metadata' => $attributes['metadata'] ?? $entry->metadata,
            ])->save();

            $entry->revisions()->create([
                'created_by' => $actor->id,
                'title' => $entry->title,
                'body' => $entry->body,
                'data' => [
                    'excerpt' => $entry->excerpt,
                    'status' => $entry->status,
                    'visibility' => $entry->visibility,
                    'seo' => $entry->seo,
                    'metadata' => $entry->metadata,
                ],
            ]);

            $this->activityLogger->log(
                action: 'content_entry.updated',
                organizationId: $entry->organization_id,
                siteId: $entry->site_id,
                actorUserId: $actor->id,
                description: 'Content entry updated',
                subject: $entry,
            );

            return $entry->refresh();
        });
    }
}
