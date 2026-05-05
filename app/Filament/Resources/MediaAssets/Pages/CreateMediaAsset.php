<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\MediaService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CreateMediaAsset extends CreateRecord
{
    protected static string $resource = MediaAssetResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        if (! ($user instanceof User)) {
            abort(403);
        }

        $disk = 'public';
        $relativePath = (string) ($this->data['uploaded_file'] ?? '');

        if ($relativePath === '') {
            abort(422, 'Uploaded file is required.');
        }

        $storage = Storage::disk($disk);
        $fileName = basename($relativePath);
        $mimeType = (string) $storage->mimeType($relativePath);
        $sizeBytes = (int) $storage->size($relativePath);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $fullPath = $storage->path($relativePath);
        $dimensions = @getimagesize($fullPath) ?: [null, null];

        $organizationId = (int) $data['organization_id'];
        $siteId = $data['site_id'] ?? null;
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

        return app(MediaService::class)->createAsset([
            'organization_id' => $organizationId,
            'site_id' => $siteId,
            'company_id' => $companyId,
            'uploaded_by' => $user->id,
            'disk' => $disk,
            'path' => $relativePath,
            'relative_path' => $relativePath,
            'filename' => $fileName,
            'original_filename' => $fileName,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size_bytes' => $sizeBytes,
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,
            'visibility' => $data['visibility'] ?? 'private',
            'alt_text' => $data['alt_text'] ?? null,
            'caption' => $data['caption'] ?? null,
            'checksum' => sha1($relativePath.'|'.$sizeBytes),
            'metadata' => ['source' => 'filament_upload'],
        ]);
    }
}
