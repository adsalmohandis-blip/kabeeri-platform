<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Modules\BusinessOperations\Services\CrmLeadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CrmLeadsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_can_be_created_for_organization_and_contact(): void
    {
        $organization = Organization::factory()->create();
        $contact = Contact::factory()->create(['organization_id' => $organization->id]);

        $lead = app(CrmLeadService::class)->createForOrganization($organization, [
            'contact_id' => $contact->id,
            'title' => 'Website redesign opportunity',
            'name' => 'Nadia Customer',
            'email' => 'nadia@example.com',
            'priority' => 'high',
            'expected_value' => 25000,
            'currency_code' => 'EGP',
        ]);

        $this->assertTrue($lead->organization->is($organization));
        $this->assertTrue($lead->contact->is($contact));
        $this->assertSame('manual', $lead->source);
        $this->assertSame('new', $lead->status);
        $this->assertSame('high', $lead->priority);
        $this->assertSame('25000.00', $lead->expected_value);
    }

    public function test_lead_contact_must_belong_to_same_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherContact = Contact::factory()->create();

        $this->expectException(ValidationException::class);

        app(CrmLeadService::class)->createForOrganization($organization, [
            'contact_id' => $otherContact->id,
            'name' => 'Wrong tenant',
        ]);
    }

    public function test_lead_can_move_through_simple_crm_statuses(): void
    {
        $lead = Lead::factory()->create(['status' => 'new']);
        $service = app(CrmLeadService::class);

        $qualified = $service->qualify($lead);
        $this->assertSame('qualified', $qualified->status);
        $this->assertNotNull($qualified->qualified_at);

        $lost = $service->markLost($qualified, 'Budget mismatch');
        $this->assertSame('lost', $lost->status);
        $this->assertSame('Budget mismatch', $lost->lost_reason);
        $this->assertNotNull($lost->lost_at);

        $converted = $service->convert($lost);
        $this->assertSame('converted', $converted->status);
        $this->assertNotNull($converted->converted_at);
        $this->assertNull($converted->lost_at);
        $this->assertNull($converted->lost_reason);
    }
}
