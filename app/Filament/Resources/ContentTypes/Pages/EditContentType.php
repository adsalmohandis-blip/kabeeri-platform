<?php

namespace App\Filament\Resources\ContentTypes\Pages;

use App\Filament\Resources\ContentTypes\ContentTypeResource;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditContentType extends EditRecord
{
    protected static string $resource = ContentTypeResource::class;

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
        $targetSiteId = $data['site_id'] ?? null;

        if ($targetOrganizationId !== (int) $record->organization_id) {
            $isOwnerOfTargetOrganization = Organization::query()
                ->where('id', $targetOrganizationId)
                ->where('owner_user_id', $user->id)
                ->exists();

            if (! $isOwnerOfTargetOrganization) {
                abort(403);
            }
        }

        if ($targetSiteId !== null) {
            $siteBelongsToOrganization = Site::query()
                ->where('id', $targetSiteId)
                ->where('organization_id', $targetOrganizationId)
                ->exists();

            if (! $siteBelongsToOrganization) {
                throw ValidationException::withMessages([
                    'site_id' => 'The selected app does not belong to the selected organization.',
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
