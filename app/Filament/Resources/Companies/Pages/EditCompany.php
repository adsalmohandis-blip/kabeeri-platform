<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use App\Models\Organization;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

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

        if ($targetOrganizationId !== (int) $record->organization_id) {
            $isOwnerOfTargetOrganization = Organization::query()
                ->where('id', $targetOrganizationId)
                ->where('owner_user_id', $user->id)
                ->exists();

            if (! $isOwnerOfTargetOrganization) {
                abort(403);
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
