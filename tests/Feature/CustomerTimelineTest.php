<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\CrmActivity;
use App\Models\Lead;
use App\Modules\BusinessOperations\Services\CustomerTimelineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_timeline_collects_contact_leads_and_activities(): void
    {
        $contact = Contact::factory()->create([
            'display_name' => 'Nadia Customer',
            'created_at' => now()->subMinutes(30),
        ]);
        $lead = Lead::factory()->create([
            'organization_id' => $contact->organization_id,
            'contact_id' => $contact->id,
            'title' => 'Website project',
            'created_at' => now()->subHour(),
        ]);
        CrmActivity::factory()->create([
            'organization_id' => $contact->organization_id,
            'contact_id' => $contact->id,
            'lead_id' => $lead->id,
            'activity_type' => 'call',
            'subject' => 'Discovery call',
            'created_at' => now(),
        ]);

        $timeline = app(CustomerTimelineService::class)->forContact($contact);

        $this->assertSame([
            'crm_activity.call',
            'contact.created',
            'lead.created',
        ], $timeline->pluck('type')->all());
        $this->assertSame('Discovery call', $timeline->first()['subject']);
    }

    public function test_customer_timeline_includes_stage_change_event(): void
    {
        $contact = Contact::factory()->create();
        $lead = Lead::factory()->create([
            'organization_id' => $contact->organization_id,
            'contact_id' => $contact->id,
            'stage_changed_at' => now()->addMinute(),
        ]);

        $timeline = app(CustomerTimelineService::class)->forContact($contact);

        $stageChange = $timeline->firstWhere('type', 'lead.stage_changed');
        $this->assertNotNull($stageChange);
        $this->assertSame($lead->id, $stageChange['record_id']);
    }
}
