<?php

namespace App\Modules\Mall\Services;

use App\Models\Organization;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function submit(Organization $organization, Model $reviewable, ?User $reviewer = null, array $attributes = []): Review
    {
        $this->assertTenantMatch($organization, $reviewable);
        $this->assertValidRating($attributes['rating'] ?? null);

        return Review::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'site_id' => $attributes['site_id'] ?? $reviewable->getAttribute('site_id'),
            'reviewable_type' => $reviewable->getMorphClass(),
            'reviewable_id' => $reviewable->getKey(),
            'reviewer_user_id' => $reviewer?->id,
            'source' => $attributes['source'] ?? 'manual',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
    }

    public function approve(Review $review): Review
    {
        $review->forceFill([
            'status' => 'published',
            'published_at' => now(),
            'rejected_at' => null,
        ])->save();

        return $review->refresh();
    }

    public function reject(Review $review, ?string $reason = null): Review
    {
        $metadata = $review->metadata ?? [];

        if ($reason !== null) {
            $metadata['rejection_reason'] = $reason;
        }

        $review->forceFill([
            'status' => 'rejected',
            'metadata' => $metadata,
            'published_at' => null,
            'rejected_at' => now(),
        ])->save();

        return $review->refresh();
    }

    private function assertTenantMatch(Organization $organization, Model $reviewable): void
    {
        if (! $reviewable->offsetExists('organization_id')) {
            throw ValidationException::withMessages([
                'reviewable' => 'Reviews require a tenant-scoped target.',
            ]);
        }

        if ((int) $reviewable->getAttribute('organization_id') !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'reviewable' => 'The review target does not belong to this organization.',
            ]);
        }
    }

    private function assertValidRating(mixed $rating): void
    {
        if (! is_numeric($rating) || (int) $rating < 1 || (int) $rating > 5) {
            throw ValidationException::withMessages([
                'rating' => 'Rating must be between 1 and 5.',
            ]);
        }
    }
}
