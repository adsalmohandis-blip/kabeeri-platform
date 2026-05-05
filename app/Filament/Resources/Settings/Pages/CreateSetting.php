<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['scope_type'] ?? null) === 'organization' && empty($data['scope_id'])) {
            $data['scope_id'] = $data['organization_id'] ?? null;
        }

        if (($data['scope_type'] ?? null) === 'platform') {
            abort(403);
        }

        return $data;
    }
}
