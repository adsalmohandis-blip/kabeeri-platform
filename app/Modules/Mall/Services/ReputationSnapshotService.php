<?php

namespace App\Modules\Mall\Services;

use App\Models\ModerationCase;
use App\Models\Organization;
use App\Models\ReputationSnapshot;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ReputationSnapshotService
{
    public function calculate(Organization $organization, Model $subject): ReputationSnapshot
    {
        $this->assertTenantMatch($organization, $subject);

        $reviewQuery = Review::query()
            ->where('organization_id', $organization->id)
            ->where('reviewable_type', $subject->getMorphClass())
            ->where('reviewable_id', $subject->getKey());

        $publishedReviewQuery = (clone $reviewQuery)->where('status', 'published');
        $publishedReviewCount = (int) $publishedReviewQuery->count();
        $averageRating = $publishedReviewCount > 0
            ? round((float) $publishedReviewQuery->avg('rating'), 2)
            : null;

        $openCasesCount = (int) ModerationCase::query()
            ->where('organization_id', $organization->id)
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey())
            ->whereIn('status', ['open', 'in_review'])
            ->count();

        return ReputationSnapshot::query()->updateOrCreate(
            [
                'subject_type' => $subject->getMorphClass(),
                'subject_id' => $subject->getKey(),
            ],
            [
                'organization_id' => $organization->id,
                'site_id' => $subject->getAttribute('site_id'),
                'review_count' => (int) $reviewQuery->count(),
                'published_review_count' => $publishedReviewCount,
                'average_rating' => $averageRating,
                'open_moderation_cases_count' => $openCasesCount,
                'trust_score' => $this->score($averageRating, $publishedReviewCount, $openCasesCount),
                'calculated_at' => now(),
                'metadata' => ['strategy' => 'v4_basic'],
            ],
        );
    }

    private function score(?float $averageRating, int $publishedReviewCount, int $openCasesCount): int
    {
        $base = $averageRating === null ? 50 : (int) round($averageRating * 20);
        $reviewBonus = min(10, $publishedReviewCount);
        $moderationPenalty = min(40, $openCasesCount * 10);

        return max(0, min(100, $base + $reviewBonus - $moderationPenalty));
    }

    private function assertTenantMatch(Organization $organization, Model $subject): void
    {
        if (! $subject->offsetExists('organization_id')) {
            throw ValidationException::withMessages([
                'subject' => 'Reputation snapshots require a tenant-scoped subject.',
            ]);
        }

        if ((int) $subject->getAttribute('organization_id') !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'subject' => 'The reputation subject does not belong to this organization.',
            ]);
        }
    }
}
