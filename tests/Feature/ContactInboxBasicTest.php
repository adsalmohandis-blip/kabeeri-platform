<?php

namespace Tests\Feature;

use App\Models\FormSubmission;
use App\Models\Lead;
use App\Modules\CMS\Services\ContactInboxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactInboxBasicTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_can_be_marked_read_closed_and_spam(): void
    {
        $submission = FormSubmission::factory()->create(['status' => 'new']);
        $service = app(ContactInboxService::class);

        $this->assertSame('read', $service->markRead($submission)->status);
        $this->assertSame('closed', $service->markClosed($submission)->status);
        $this->assertSame('spam', $service->markSpam($submission)->status);
    }

    public function test_lead_can_be_marked_read_in_progress_closed_and_spam(): void
    {
        $lead = Lead::factory()->create(['status' => 'new']);
        $service = app(ContactInboxService::class);

        $this->assertSame('read', $service->markRead($lead)->status);
        $this->assertSame('in_progress', $service->markInProgress($lead)->status);
        $this->assertSame('closed', $service->markClosed($lead)->status);
        $this->assertSame('spam', $service->markSpam($lead)->status);
    }
}
