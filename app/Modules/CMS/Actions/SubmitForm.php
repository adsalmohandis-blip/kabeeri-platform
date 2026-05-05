<?php

namespace App\Modules\CMS\Actions;

use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use App\Models\User;
use App\Modules\Core\Services\ActivityLogger;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SubmitForm
{
    public function __construct(
        protected ActivityLogger $activityLogger,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>|null  $metadata
     */
    public function __invoke(
        Form $form,
        array $data,
        ?User $submitter = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $sourceUrl = null,
        ?array $metadata = null,
    ): FormSubmission {
        if ($form->status !== 'active') {
            throw ValidationException::withMessages([
                'form' => 'The form is not accepting submissions.',
            ]);
        }

        $validated = Validator::make($data, $this->rulesFor($form))->validate();

        $submission = FormSubmission::query()->create([
            'organization_id' => $form->organization_id,
            'site_id' => $form->site_id,
            'form_id' => $form->id,
            'submitted_by_user_id' => $submitter?->id,
            'data' => $validated,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'status' => 'new',
            'source_url' => $sourceUrl,
            'metadata' => $metadata,
        ]);

        $this->activityLogger->log(
            action: 'form.submitted',
            organizationId: $submission->organization_id,
            siteId: $submission->site_id,
            actorUserId: $submitter?->id,
            description: 'Form submitted',
            subject: $submission,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
        );

        return $submission->refresh();
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rulesFor(Form $form): array
    {
        return $form->fields()
            ->get()
            ->mapWithKeys(function (FormField $field): array {
                $rules = is_array($field->validation_rules) ? $field->validation_rules : [];
                array_unshift($rules, $field->is_required ? 'required' : 'nullable');

                return [$field->name => array_values(array_unique($rules))];
            })
            ->all();
    }
}
