<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if (! ($user instanceof User)) {
            abort(403);
        }

        if (($data['scope_type'] ?? null) === 'organization' && empty($data['scope_id'])) {
            $data['scope_id'] = $data['organization_id'] ?? null;
        }

        if (($data['scope_type'] ?? null) === 'platform') {
            abort(403);
        }

        $organizationId = (int) ($data['organization_id'] ?? 0);

        $canAccessOrganization = Organization::query()
            ->where('id', $organizationId)
            ->where(function (Builder $query) use ($user): void {
                $query
                    ->where('owner_user_id', $user->id)
                    ->orWhereHas('memberships', function (Builder $membershipQuery) use ($user): void {
                        $membershipQuery
                            ->where('user_id', $user->id)
                            ->where('status', 'active');
                    });
            })
            ->exists();

        if (! $canAccessOrganization) {
            abort(403);
        }

        $scopeType = (string) ($data['scope_type'] ?? '');
        $scopeId = $data['scope_id'] ?? null;

        if ($scopeType === 'site' && $scopeId !== null) {
            $isSiteInOrganization = Site::query()
                ->where('id', $scopeId)
                ->where('organization_id', $organizationId)
                ->exists();

            if (! $isSiteInOrganization) {
                throw ValidationException::withMessages([
                    'scope_id' => 'The selected site scope is outside the selected organization.',
                ]);
            }
        }

        if ($scopeType === 'company' && $scopeId !== null) {
            $isCompanyInOrganization = Company::query()
                ->where('id', $scopeId)
                ->where('organization_id', $organizationId)
                ->exists();

            if (! $isCompanyInOrganization) {
                throw ValidationException::withMessages([
                    'scope_id' => 'The selected company scope is outside the selected organization.',
                ]);
            }
        }

        return $data;
    }
}
