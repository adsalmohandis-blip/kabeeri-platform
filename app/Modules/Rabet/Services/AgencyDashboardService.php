<?php

namespace App\Modules\Rabet\Services;

use App\Models\AgencyDashboardSnapshot;
use App\Models\AgencyPartnerProfile;

class AgencyDashboardService
{
    public function snapshot(AgencyPartnerProfile $profile): AgencyDashboardSnapshot
    {
        $metrics = [
            'status' => $profile->status,
            'accreditation_status' => $profile->accreditation_status,
            'accreditation_level' => $profile->accreditation_level,
            'service_categories_count' => count($profile->service_categories ?? []),
            'regions_count' => count($profile->regions ?? []),
            'languages_count' => count($profile->languages ?? []),
        ];

        return AgencyDashboardSnapshot::query()->create([
            'organization_id' => $profile->organization_id,
            'agency_partner_profile_id' => $profile->id,
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'metrics' => $metrics,
            'alerts' => $this->alerts($profile),
            'generated_at' => now(),
            'metadata' => ['strategy' => 'v4_basic'],
        ]);
    }

    /**
     * @return list<array{type: string, message: string}>
     */
    private function alerts(AgencyPartnerProfile $profile): array
    {
        if ($profile->accreditation_status === 'accredited') {
            return [];
        }

        return [[
            'type' => 'accreditation',
            'message' => 'Agency accreditation is not complete.',
        ]];
    }
}
