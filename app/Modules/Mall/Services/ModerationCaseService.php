<?php

namespace App\Modules\Mall\Services;

use App\Models\ModerationCase;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ModerationCaseService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function open(Organization $organization, ?Model $subject = null, array $attributes = []): ModerationCase
    {
        $this->assertTenantMatch($organization, $subject);

        return ModerationCase::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'site_id' => $attributes['site_id'] ?? $subject?->getAttribute('site_id'),
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'case_type' => $attributes['case_type'] ?? 'content',
            'priority' => $attributes['priority'] ?? 'normal',
            'status' => 'open',
            'opened_at' => now(),
        ]);
    }

    public function assign(ModerationCase $case, User $user): ModerationCase
    {
        $case->forceFill([
            'assigned_to_user_id' => $user->id,
            'status' => $case->status === 'open' ? 'in_review' : $case->status,
        ])->save();

        return $case->refresh();
    }

    /**
     * @param  array<string, mixed>  $resolution
     */
    public function resolve(ModerationCase $case, array $resolution): ModerationCase
    {
        $case->forceFill([
            'status' => 'resolved',
            'resolution' => $resolution,
            'resolved_at' => now(),
        ])->save();

        return $case->refresh();
    }

    private function assertTenantMatch(Organization $organization, ?Model $subject): void
    {
        if ($subject === null || ! $subject->offsetExists('organization_id')) {
            return;
        }

        if ((int) $subject->getAttribute('organization_id') !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'subject' => 'The moderation subject does not belong to this organization.',
            ]);
        }
    }
}
