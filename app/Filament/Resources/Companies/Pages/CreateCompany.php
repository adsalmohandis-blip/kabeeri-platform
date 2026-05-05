<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use App\Models\Organization;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

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

        return static::getModel()::query()->create($data);
    }
}
