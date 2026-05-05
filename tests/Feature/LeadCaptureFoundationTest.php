<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\FormField;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Site;
use App\Modules\CMS\Actions\SubmitForm;
use App\Modules\CMS\Services\LeadCaptureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadCaptureFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_submission_can_create_lead_when_enabled(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);
        $form = Form::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'status' => 'active',
            'settings' => ['lead_capture' => true],
        ]);

        foreach (['name', 'email', 'phone', 'message'] as $name) {
            FormField::factory()->create([
                'form_id' => $form->id,
                'name' => $name,
                'is_required' => $name === 'email',
            ]);
        }

        $submission = app(SubmitForm::class)(
            form: $form,
            data: [
                'name' => 'Ada Lovelace',
                'email' => 'ada@example.test',
                'phone' => '+201000000000',
                'message' => 'I need a quote.',
            ],
            sourceUrl: 'https://example.test/contact',
        );

        $lead = app(LeadCaptureService::class)->captureFromSubmission($submission);

        $this->assertInstanceOf(Lead::class, $lead);
        $this->assertSame($organization->id, $lead->organization_id);
        $this->assertSame($site->id, $lead->site_id);
        $this->assertSame($submission->id, $lead->form_submission_id);
        $this->assertSame('Ada Lovelace', $lead->name);
        $this->assertSame('ada@example.test', $lead->email);
        $this->assertSame('+201000000000', $lead->phone);
        $this->assertSame('I need a quote.', $lead->message);
        $this->assertTrue($organization->leads()->whereKey($lead->id)->exists());
        $this->assertTrue($site->leads()->whereKey($lead->id)->exists());
        $this->assertTrue($submission->lead()->whereKey($lead->id)->exists());
    }

    public function test_lead_capture_is_optional_per_form(): void
    {
        $form = Form::factory()->create([
            'status' => 'active',
            'settings' => ['lead_capture' => false],
        ]);
        FormField::factory()->create([
            'form_id' => $form->id,
            'name' => 'email',
            'is_required' => true,
        ]);

        $submission = app(SubmitForm::class)(
            form: $form,
            data: ['email' => 'visitor@example.test'],
        );

        $this->assertNull(app(LeadCaptureService::class)->captureFromSubmission($submission));
        $this->assertSame(0, Lead::query()->count());
    }
}
