<?php

namespace App\Modules\CMS\Services;

use App\Models\FormSubmission;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ContactInboxService
{
    public const STATUSES = [
        'new',
        'read',
        'in_progress',
        'closed',
        'spam',
    ];

    public function markRead(FormSubmission|Lead $record): FormSubmission|Lead
    {
        return $this->markStatus($record, 'read');
    }

    public function markInProgress(FormSubmission|Lead $record): FormSubmission|Lead
    {
        return $this->markStatus($record, 'in_progress');
    }

    public function markClosed(FormSubmission|Lead $record): FormSubmission|Lead
    {
        return $this->markStatus($record, 'closed');
    }

    public function markSpam(FormSubmission|Lead $record): FormSubmission|Lead
    {
        return $this->markStatus($record, 'spam');
    }

    protected function markStatus(FormSubmission|Lead $record, string $status): FormSubmission|Lead
    {
        if (! in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException("Unsupported inbox status [{$status}].");
        }

        /** @var Model $record */
        $record->forceFill(['status' => $status])->save();

        return $record->refresh();
    }
}
