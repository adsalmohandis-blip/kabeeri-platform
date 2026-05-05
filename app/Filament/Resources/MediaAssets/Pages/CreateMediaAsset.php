<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Models\User;
use App\Modules\Core\Services\MediaService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

        return app(MediaService::class)->createAsset([
            'organization_id' => $data['organization_id'],
            'site_id' => $data['site_id'] ?? null,
            'company_id' => $data['company_id'] ?? null,
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
