<?php

namespace App\Modules\Mall\Services;

use App\Models\ModerationCase;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ModerationQueueService
{
    public function queryForOrganization(Organization $organization): Builder
    {
        return ModerationCase::query()
            ->where('organization_id', $organization->id)
            ->whereIn('status', ['open', 'in_review'])
            ->orderByRaw(
                "case priority when 'urgent' then 0 when 'high' then 1 when 'normal' then 2 else 3 end",
            )
            ->orderBy('opened_at')
            ->orderBy('id');
    }

    public function pendingCount(Organization $organization): int
    {
        return (int) $this->queryForOrganization($organization)->count();
    }

    public function takeNext(Organization $organization, User $assignee): ?ModerationCase
    {
        $case = $this->queryForOrganization($organization)
            ->where('status', 'open')
            ->first();

        if ($case === null) {
            return null;
        }

        return app(ModerationCaseService::class)->assign($case, $assignee);
    }

    /**
     * @return array<string, int>
     */
    public function statusSummary(Organization $organization): array
    {
        return ModerationCase::query()
            ->where('organization_id', $organization->id)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn (mixed $count): int => (int) $count)
            ->all();
    }
}
