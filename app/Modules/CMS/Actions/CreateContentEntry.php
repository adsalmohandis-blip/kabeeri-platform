<?php

namespace App\Modules\CMS\Actions;

use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\ActivityLogger;
use App\Modules\Core\Services\PermissionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class CreateContentEntry
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
    public function __invoke(
        User $actor,
        Site $site,
        ContentType $contentType,
        array $attributes,
    ): ContentEntry {
        if (
            ! $this->permissionService->hasSitePermission($actor, $site->id, 'content.create')
            && $site->organization->owner_user_id !== $actor->id
        ) {
            throw new AuthorizationException('You are not allowed to create content in this app.');
        }

        if (
            $contentType->organization_id !== $site->organization_id
            || ($contentType->site_id !== null && $contentType->site_id !== $site->id)
        ) {
            throw new AuthorizationException('Content type does not belong to the target app.');
        }

        return DB::transaction(function () use ($actor, $site, $contentType, $attributes): ContentEntry {
            $entry = ContentEntry::query()->create([
                'organization_id' => $site->organization_id,
                'site_id' => $site->id,
                'content_type_id' => $contentType->id,
                'author_user_id' => $attributes['author_user_id'] ?? $actor->id,
                'title' => $attributes['title'],
                'slug' => $attributes['slug'],
                'excerpt' => $attributes['excerpt'] ?? null,
                'body' => $attributes['body'] ?? null,
                'status' => $attributes['status'] ?? 'draft',
                'visibility' => $attributes['visibility'] ?? 'public',
                'published_at' => $attributes['published_at'] ?? null,
                'seo' => $attributes['seo'] ?? null,
                'metadata' => $attributes['metadata'] ?? null,
            ]);

            $this->activityLogger->log(
                action: 'content_entry.created',
                organizationId: $entry->organization_id,
                siteId: $entry->site_id,
                actorUserId: $actor->id,
                description: 'Content entry created',
                subject: $entry,
            );

            return $entry->refresh();
        });
    }
}
