<?php

namespace App\Filament\Resources\Sites\Pages;

use App\Filament\Resources\Sites\SiteResource;
use App\Models\Company;
use App\Models\Organization;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditSite extends EditRecord
{
    protected static string $resource = SiteResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();

        if (! ($user instanceof User)) {
            abort(403);
        }

        $targetOrganizationId = (int) ($data['organization_id'] ?? $record->organization_id);
        $targetCompanyId = $data['company_id'] ?? null;

        if ($targetOrganizationId !== (int) $record->organization_id) {
            $isOwnerOfTargetOrganization = Organization::query()
                ->where('id', $targetOrganizationId)
                ->where('owner_user_id', $user->id)
                ->exists();

            if (! $isOwnerOfTargetOrganization) {
                abort(403);
            }
        }

        if ($targetCompanyId !== null) {
            $companyBelongsToOrganization = Company::query()
                ->where('id', $targetCompanyId)
                ->where('organization_id', $targetOrganizationId)
                ->exists();

            if (! $companyBelongsToOrganization) {
                throw ValidationException::withMessages([
                    'company_id' => 'The selected company does not belong to the selected organization.',
                ]);
            }
        }

        $record->fill($data)->save();

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}
