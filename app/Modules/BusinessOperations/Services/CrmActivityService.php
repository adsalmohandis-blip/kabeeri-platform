<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Contact;
use App\Models\CrmActivity;
use App\Models\Lead;
use App\Models\Organization;
use Illuminate\Validation\ValidationException;

class CrmActivityService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForOrganization(Organization $organization, array $attributes): CrmActivity
    {
        foreach (['contact_id' => 'contact', 'lead_id' => 'lead'] as $key => $relation) {
            if (($attributes[$key] ?? null) === null) {
                continue;
            }

            $model = $relation === 'contact'
                ? Contact::query()->findOrFail($attributes[$key])
                : Lead::query()->findOrFail($attributes[$key]);

            if ((int) $model->organization_id !== (int) $organization->id) {
                throw ValidationException::withMessages([
                    $key => 'The selected CRM record does not belong to this organization.',
                ]);
            }
        }

        return CrmActivity::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'status' => $attributes['status'] ?? 'open',
            'priority' => $attributes['priority'] ?? 'normal',
        ]);
    }

    public function complete(CrmActivity $activity): CrmActivity
    {
        $activity->forceFill([
            'status' => 'completed',
            'completed_at' => now(),
        ])->save();

        return $activity->refresh();
    }

    public function reopen(CrmActivity $activity): CrmActivity
    {
        $activity->forceFill([
            'status' => 'open',
            'completed_at' => null,
        ])->save();

        return $activity->refresh();
    }
}
