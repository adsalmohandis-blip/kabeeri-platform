<?php

namespace App\Modules\CMS\Services;

use App\Models\FormSubmission;
use App\Models\Lead;

class LeadCaptureService
{
    public function shouldCapture(FormSubmission $submission): bool
    {
        $settings = is_array($submission->form?->settings) ? $submission->form->settings : [];

        return (bool) ($settings['lead_capture'] ?? false);
    }

    public function captureFromSubmission(FormSubmission $submission, bool $force = false): ?Lead
    {
        if (! $force && ! $this->shouldCapture($submission)) {
            return null;
        }

        $data = is_array($submission->data) ? $submission->data : [];

        return Lead::query()->firstOrCreate(
            ['form_submission_id' => $submission->id],
            [
                'organization_id' => $submission->organization_id,
                'site_id' => $submission->site_id,
                'company_id' => null,
                'name' => $data['name'] ?? $data['full_name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'source' => 'form',
                'status' => 'new',
                'score' => null,
                'assigned_to' => null,
                'message' => $data['message'] ?? null,
                'metadata' => [
                    'form_id' => $submission->form_id,
                    'source_url' => $submission->source_url,
                ],
            ],
        );
    }
}
