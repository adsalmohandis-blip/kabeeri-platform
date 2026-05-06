<?php

namespace App\Modules\Mall\Services;

use App\Models\ModerationFlag;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ModerationFlagService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function report(Organization $organization, Model $subject, array $attributes = [], bool $openCase = true): ModerationFlag
    {
        $this->assertTenantMatch($organization, $subject);

        $case = null;

        if ($openCase) {
            $case = app(ModerationCaseService::class)->open($organization, $subject, [
                'case_type' => $attributes['flag_type'] ?? 'content_report',
                'reason' => $attributes['reason'] ?? null,
                'priority' => $attributes['severity'] ?? 'normal',
            ]);
        }

        return ModerationFlag::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'site_id' => $attributes['site_id'] ?? $subject->getAttribute('site_id'),
            'moderation_case_id' => $case?->id,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'flag_type' => $attributes['flag_type'] ?? 'content_report',
            'severity' => $attributes['severity'] ?? 'normal',
            'status' => 'new',
        ]);
    }

    public function markReviewed(ModerationFlag $flag): ModerationFlag
    {
        $flag->forceFill([
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ])->save();

        return $flag->refresh();
    }

    public function dismiss(ModerationFlag $flag, ?string $reason = null): ModerationFlag
    {
        $metadata = $flag->metadata ?? [];

        if ($reason !== null) {
            $metadata['dismissed_reason'] = $reason;
        }

        $flag->forceFill([
            'status' => 'dismissed',
            'metadata' => $metadata,
            'reviewed_at' => now(),
        ])->save();

        return $flag->refresh();
    }

    private function assertTenantMatch(Organization $organization, Model $subject): void
    {
        if (! $subject->offsetExists('organization_id')) {
            throw ValidationException::withMessages([
                'subject' => 'Moderation flags require a tenant-scoped subject.',
            ]);
        }

        if ((int) $subject->getAttribute('organization_id') !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'subject' => 'The moderation subject does not belong to this organization.',
            ]);
        }
    }
}
