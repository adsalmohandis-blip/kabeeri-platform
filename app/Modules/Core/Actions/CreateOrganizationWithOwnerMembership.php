<?php

namespace App\Modules\Core\Actions;

use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateOrganizationWithOwnerMembership
{
    /**
     * @param  array<string, mixed>  $organizationData
     */
    public function __invoke(array $organizationData, User $owner): Organization
    {
        return DB::transaction(function () use ($organizationData, $owner): Organization {
            $organization = Organization::query()->create(array_merge(
                $organizationData,
                ['owner_user_id' => $owner->id],
            ));

            OrganizationMembership::query()->updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'user_id' => $owner->id,
                ],
                [
                    'membership_type' => 'owner',
                    'status' => 'active',
                    'accepted_at' => now(),
                    'start_date' => now()->toDateString(),
                    'visibility' => 'organization_only',
                ],
            );

            return $organization->load('memberships');
        });
    }
}
