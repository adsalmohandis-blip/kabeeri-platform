<?php

namespace App\Filament\Resources\ContentTypes\Pages;

use App\Filament\Resources\ContentTypes\ContentTypeResource;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateContentType extends CreateRecord
{
    protected static string $resource = ContentTypeResource::class;

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
        $siteId = $data['site_id'] ?? null;

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

        if ($siteId !== null) {
            $siteBelongsToOrganization = Site::query()
                ->where('id', $siteId)
                ->where('organization_id', $organizationId)
                ->exists();

            if (! $siteBelongsToOrganization) {
                throw ValidationException::withMessages([
                    'site_id' => 'The selected app does not belong to the selected organization.',
                ]);
            }
        }

        return static::getModel()::query()->create($data);
    }
}
