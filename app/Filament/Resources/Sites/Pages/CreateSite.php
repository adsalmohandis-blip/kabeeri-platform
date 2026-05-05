<?php

namespace App\Filament\Resources\Sites\Pages;

use App\Filament\Resources\Sites\SiteResource;
use App\Models\Company;
use App\Models\Organization;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateSite extends CreateRecord
{
    protected static string $resource = SiteResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        if (! ($user instanceof User)) {
            abort(403);
        }

        $organizationId = (int) ($data['organization_id'] ?? 0);
        $companyId = $data['company_id'] ?? null;

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

        if ($companyId !== null) {
            $companyBelongsToOrganization = Company::query()
                ->where('id', $companyId)
                ->where('organization_id', $organizationId)
                ->exists();

            if (! $companyBelongsToOrganization) {
                throw ValidationException::withMessages([
                    'company_id' => 'The selected company does not belong to the selected organization.',
                ]);
            }
        }

        $data['created_by'] = $user->id;

        return static::getModel()::query()->create($data);
    }
}
