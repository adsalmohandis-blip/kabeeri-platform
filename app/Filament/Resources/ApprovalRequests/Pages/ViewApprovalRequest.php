<?php

namespace App\Filament\Resources\ApprovalRequests\Pages;

use App\Filament\Resources\ApprovalRequests\ApprovalRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewApprovalRequest extends ViewRecord
{
    protected static string $resource = ApprovalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
