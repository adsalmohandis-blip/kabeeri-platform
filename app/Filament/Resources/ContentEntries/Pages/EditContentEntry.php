<?php

namespace App\Filament\Resources\ContentEntries\Pages;

use App\Filament\Resources\ContentEntries\ContentEntryResource;
use App\Models\ContentEntry;
use App\Models\User;
use App\Modules\CMS\Actions\PublishContentEntry as PublishContentEntryAction;
use App\Modules\CMS\Actions\UpdateContentEntry as UpdateContentEntryAction;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditContentEntry extends EditRecord
{
    protected static string $resource = ContentEntryResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();

        if (! ($user instanceof User) || ! $record instanceof ContentEntry) {
            abort(403);
        }

        return app(UpdateContentEntryAction::class)(
            actor: $user,
            entry: $record,
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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label('Publish')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status !== 'published')
                ->authorize(fn (): bool => auth()->user() instanceof User && auth()->user()->can('publish', $this->record))
                ->action(function (): void {
                    $user = auth()->user();

                    if (! ($user instanceof User)) {
                        abort(403);
                    }

                    app(PublishContentEntryAction::class)(
                        actor: $user,
                        entry: $this->record,
                    );

                    $this->record->refresh();
                }),
            ViewAction::make(),
        ];
    }
}
