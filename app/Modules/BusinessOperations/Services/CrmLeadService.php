<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class CrmLeadService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForOrganization(Organization $organization, array $attributes): Lead
    {
        if (($attributes['contact_id'] ?? null) !== null) {
            $contact = Contact::query()->findOrFail($attributes['contact_id']);

            if ((int) $contact->organization_id !== (int) $organization->id) {
                throw ValidationException::withMessages([
                    'contact_id' => 'The selected contact does not belong to this organization.',
                ]);
            }
        }

        return Lead::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'status' => $attributes['status'] ?? 'new',
            'priority' => $attributes['priority'] ?? 'normal',
            'source' => $attributes['source'] ?? 'manual',
        ]);
    }

    public function qualify(Lead $lead, ?Carbon $at = null): Lead
    {
        $lead->forceFill([
            'status' => 'qualified',
            'qualified_at' => $at ?? now(),
        ])->save();

        return $lead->refresh();
    }

    public function convert(Lead $lead, ?Carbon $at = null): Lead
    {
        $lead->forceFill([
            'status' => 'converted',
            'converted_at' => $at ?? now(),
            'lost_at' => null,
            'lost_reason' => null,
        ])->save();

        return $lead->refresh();
    }

    public function markLost(Lead $lead, string $reason, ?Carbon $at = null): Lead
    {
        $lead->forceFill([
            'status' => 'lost',
            'lost_at' => $at ?? now(),
            'lost_reason' => $reason,
        ])->save();

        return $lead->refresh();
    }
}
