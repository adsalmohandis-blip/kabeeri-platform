<?php

namespace App\Filament\Resources\Sites\Pages;

use App\Filament\Resources\Sites\SiteResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

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

        $data['created_by'] = $user->id;

        return static::getModel()::query()->create($data);
    }
}
