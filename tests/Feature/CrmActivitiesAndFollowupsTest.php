<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\CrmActivity;
use App\Models\Lead;
use App\Models\Organization;
use App\Modules\BusinessOperations\Services\CrmActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CrmActivitiesAndFollowupsTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_can_be_created_for_contact_and_lead(): void
    {
        $organization = Organization::factory()->create();
        $contact = Contact::factory()->create(['organization_id' => $organization->id]);
        $lead = Lead::factory()->create([
            'organization_id' => $organization->id,
            'contact_id' => $contact->id,
        ]);

        $activity = app(CrmActivityService::class)->createForOrganization($organization, [
            'contact_id' => $contact->id,
            'lead_id' => $lead->id,
            'activity_type' => 'call',
            'subject' => 'Follow up on proposal',
            'due_at' => now()->addDay(),
        ]);

        $this->assertNotNull($activity->ulid);
        $this->assertTrue($activity->organization->is($organization));
        $this->assertTrue($activity->contact->is($contact));
        $this->assertTrue($activity->lead->is($lead));
        $this->assertSame('open', $activity->status);
    }

    public function test_activity_cannot_link_records_from_another_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherContact = Contact::factory()->create();

        $this->expectException(ValidationException::class);

        app(CrmActivityService::class)->createForOrganization($organization, [
            'contact_id' => $otherContact->id,
            'subject' => 'Wrong tenant',
        ]);
    }

    public function test_activity_can_be_completed_and_reopened(): void
    {
        $activity = CrmActivity::factory()->create(['status' => 'open']);
        $service = app(CrmActivityService::class);

        $completed = $service->complete($activity);
        $this->assertSame('completed', $completed->status);
        $this->assertNotNull($completed->completed_at);

        $reopened = $service->reopen($completed);
        $this->assertSame('open', $reopened->status);
        $this->assertNull($reopened->completed_at);
    }
}
