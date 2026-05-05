<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Modules\BusinessOperations\Services\ServiceRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ServiceRequestsToCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_request_can_link_to_contact_and_lead(): void
    {
        $organization = Organization::factory()->create();
        $contact = Contact::factory()->create(['organization_id' => $organization->id]);
        $lead = Lead::factory()->create([
            'organization_id' => $organization->id,
            'contact_id' => $contact->id,
        ]);

        $request = app(ServiceRequestService::class)->createForOrganization($organization, [
            'contact_id' => $contact->id,
            'lead_id' => $lead->id,
            'title' => 'Need onboarding support',
            'request_type' => 'onboarding',
        ]);

        $this->assertNotNull($request->ulid);
        $this->assertSame('SR-00001', $request->request_number);
        $this->assertTrue($request->contact->is($contact));
        $this->assertTrue($request->lead->is($lead));
        $this->assertSame('new', $request->status);
    }

    public function test_service_request_rejects_other_organization_contact(): void
    {
        $organization = Organization::factory()->create();
        $otherContact = Contact::factory()->create();

        $this->expectException(ValidationException::class);

        app(ServiceRequestService::class)->createForOrganization($organization, [
            'contact_id' => $otherContact->id,
            'title' => 'Wrong tenant',
        ]);
    }

    public function test_service_request_can_be_closed(): void
    {
        $request = app(ServiceRequestService::class)->createForOrganization(
            Organization::factory()->create(),
            ['title' => 'General help']
        );

        $closed = app(ServiceRequestService::class)->close($request);

        $this->assertSame('closed', $closed->status);
        $this->assertNotNull($closed->closed_at);
    }
}
