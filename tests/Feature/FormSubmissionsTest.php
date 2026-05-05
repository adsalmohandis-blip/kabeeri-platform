<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Organization;
use App\Models\Site;
use App\Modules\CMS\Actions\SubmitForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FormSubmissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_form_can_submit_valid_data(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $form = Form::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'status' => 'active',
        ]);
        FormField::factory()->create([
            'form_id' => $form->id,
            'label' => 'Email',
            'name' => 'email',
            'field_type' => 'email',
            'is_required' => true,
            'validation_rules' => ['email'],
        ]);
        FormField::factory()->create([
            'form_id' => $form->id,
            'label' => 'Message',
            'name' => 'message',
            'field_type' => 'textarea',
            'is_required' => false,
            'validation_rules' => ['string'],
        ]);

        $submission = app(SubmitForm::class)(
            form: $form,
            data: [
                'email' => 'visitor@example.test',
                'message' => 'Hello from the public form.',
            ],
            ipAddress: '127.0.0.1',
            userAgent: 'KABEERI Test',
            sourceUrl: 'https://example.test/contact',
        );

        $this->assertSame($organization->id, $submission->organization_id);
        $this->assertSame($site->id, $submission->site_id);
        $this->assertSame($form->id, $submission->form_id);
        $this->assertSame('new', $submission->status);
        $this->assertSame('visitor@example.test', $submission->data['email']);
        $this->assertTrue($form->submissions()->whereKey($submission->id)->exists());
        $this->assertTrue($organization->formSubmissions()->whereKey($submission->id)->exists());
        $this->assertTrue($site->formSubmissions()->whereKey($submission->id)->exists());
    }

    public function test_required_fields_are_enforced(): void
    {
        $form = Form::factory()->create(['status' => 'active']);
        FormField::factory()->create([
            'form_id' => $form->id,
            'name' => 'email',
            'field_type' => 'email',
            'is_required' => true,
            'validation_rules' => ['email'],
        ]);

        $this->expectException(ValidationException::class);

        app(SubmitForm::class)(
            form: $form,
            data: [],
        );
    }

    public function test_form_submission_writes_activity_log(): void
    {
        $form = Form::factory()->create(['status' => 'active']);
        FormField::factory()->create([
            'form_id' => $form->id,
            'name' => 'message',
            'field_type' => 'textarea',
            'is_required' => true,
        ]);

        $submission = app(SubmitForm::class)(
            form: $form,
            data: ['message' => 'Activity, please.'],
        );

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'form.submitted',
            'organization_id' => $form->organization_id,
            'site_id' => $form->site_id,
            'subject_type' => $submission->getMorphClass(),
            'subject_id' => $submission->id,
        ]);
        $this->assertSame(1, ActivityLog::query()->where('action', 'form.submitted')->count());
    }
}
