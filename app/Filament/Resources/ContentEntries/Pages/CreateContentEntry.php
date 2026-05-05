<?php

namespace App\Filament\Resources\ContentEntries\Pages;

use App\Filament\Resources\ContentEntries\ContentEntryResource;
use App\Models\ContentType;
use App\Models\Site;
use App\Models\User;
use App\Modules\CMS\Actions\CreateContentEntry as CreateContentEntryAction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateContentEntry extends CreateRecord
{
    protected static string $resource = ContentEntryResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        if (! ($user instanceof User)) {
            abort(403);
        }

        $site = Site::query()->findOrFail($data['site_id']);
        $contentType = ContentType::query()->findOrFail($data['content_type_id']);

        return app(CreateContentEntryAction::class)(
            actor: $user,
            site: $site,
            contentType: $contentType,
            attributes: [
                'title' => $data['title'],
                'slug' => $data['slug'],
                'excerpt' => $data['excerpt'] ?? null,
                'body' => $data['body'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'visibility' => $data['visibility'] ?? 'public',
                'seo' => $data['seo'] ?? null,
            ],
        );
    }
}
