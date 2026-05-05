<?php

namespace App\Filament\Resources\Organizations\Pages;

use App\Filament\Resources\Organizations\OrganizationResource;
use App\Models\User;
use App\Modules\Core\Actions\CreateOrganizationWithOwnerMembership;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        if (! ($user instanceof User)) {
            abort(403);
        }

        return app(CreateOrganizationWithOwnerMembership::class)(
            [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'account_type' => $data['account_type'] ?? 'business',
                'status' => $data['status'] ?? 'active',
                'country_id' => $data['country_id'] ?? null,
                'locale' => $data['locale'] ?? 'ar',
                'timezone' => $data['timezone'] ?? 'Africa/Cairo',
            ],
            $user,
        );
    }
}
