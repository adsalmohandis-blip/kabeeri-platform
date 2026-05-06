<?php

namespace App\Modules\Mall\Services;

use App\Models\MallMirrorBusiness;
use App\Models\MallMirrorCourse;
use App\Models\MallMirrorProduct;
use App\Models\MallMirrorService;
use App\Models\MallMirrorTalent;
use App\Models\TravelTourismMallListing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class MallMirrorStatusService
{
    private const STATUS_FIELDS = [
        MallMirrorBusiness::class => 'mirror_status',
        MallMirrorProduct::class => 'mirror_status',
        MallMirrorService::class => 'mirror_status',
        MallMirrorCourse::class => 'mirror_status',
        MallMirrorTalent::class => 'mirror_status',
        TravelTourismMallListing::class => 'listing_status',
    ];

    public function status(Model $mirror): string
    {
        $field = $this->statusField($mirror);

        return (string) $mirror->{$field};
    }

    public function markNeedsReview(Model $mirror, ?string $reason = null): Model
    {
        return $this->setStatus($mirror, 'needs_review', $reason);
    }

    public function markRejected(Model $mirror, ?string $reason = null): Model
    {
        return $this->setStatus($mirror, 'rejected', $reason);
    }

    public function archive(Model $mirror, ?string $reason = null): Model
    {
        return $this->setStatus($mirror, 'archived', $reason);
    }

    private function setStatus(Model $mirror, string $status, ?string $reason): Model
    {
        $field = $this->statusField($mirror);
        $metadata = $mirror->metadata ?? [];

        if ($reason !== null) {
            $metadata['status_reason'] = $reason;
        }

        $mirror->forceFill([
            $field => $status,
            'metadata' => $metadata,
        ])->save();

        return $mirror->refresh();
    }

    private function statusField(Model $mirror): string
    {
        $class = $mirror::class;

        if (! isset(self::STATUS_FIELDS[$class])) {
            throw ValidationException::withMessages([
                'mirror' => 'Unsupported Mall mirror status model.',
            ]);
        }

        return self::STATUS_FIELDS[$class];
    }
}
