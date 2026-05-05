<?php

namespace App\Modules\BusinessOperations\Services;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ServiceRequestService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForOrganization(Organization $organization, array $attributes): ServiceRequest
    {
        $this->assertBelongsToOrganization($organization, Contact::class, 'contact_id', $attributes);
        $this->assertBelongsToOrganization($organization, Lead::class, 'lead_id', $attributes);

        return ServiceRequest::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'request_number' => $attributes['request_number'] ?? $this->nextRequestNumber($organization),
            'status' => $attributes['status'] ?? 'new',
            'priority' => $attributes['priority'] ?? 'normal',
        ]);
    }

    public function close(ServiceRequest $request): ServiceRequest
    {
        $request->forceFill([
            'status' => 'closed',
            'closed_at' => now(),
        ])->save();

        return $request->refresh();
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $attributes
     */
    protected function assertBelongsToOrganization(
        Organization $organization,
        string $modelClass,
        string $key,
        array $attributes
    ): void {
        if (($attributes[$key] ?? null) === null) {
            return;
        }

        $model = $modelClass::query()->findOrFail($attributes[$key]);

        if ((int) $model->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                $key => 'The selected service request record does not belong to this organization.',
            ]);
        }
    }

    protected function nextRequestNumber(Organization $organization): string
    {
        $next = ServiceRequest::query()
            ->where('organization_id', $organization->id)
            ->count() + 1;

        return 'SR-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
