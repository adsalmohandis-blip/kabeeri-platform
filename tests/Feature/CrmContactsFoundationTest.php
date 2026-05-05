<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmContactsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_is_tenant_scoped_to_organization(): void
    {
        $organization = Organization::factory()->create();

        $contact = Contact::factory()->create([
            'organization_id' => $organization->id,
            'display_name' => 'Nadia Customer',
            'email' => 'nadia@example.com',
        ]);

        $this->assertNotNull($contact->ulid);
        $this->assertTrue($contact->organization->is($organization));
        $this->assertSame('customer', $contact->contact_type);
        $this->assertSame('active', $contact->status);
        $this->assertSame(['source' => 'factory'], $contact->metadata);
    }

    public function test_contact_can_be_linked_to_company_and_site(): void
    {
        $company = Company::factory()->create();
        $site = Site::factory()->create([
            'organization_id' => $company->organization_id,
            'company_id' => $company->id,
        ]);

        $contact = Contact::factory()->forSite($site)->create();

        $this->assertTrue($contact->organization->is($company->organization));
        $this->assertTrue($contact->company->is($company));
        $this->assertTrue($contact->site->is($site));
    }

    public function test_contact_email_is_unique_per_organization(): void
    {
        $firstOrganization = Organization::factory()->create();
        $secondOrganization = Organization::factory()->create();

        Contact::factory()->create([
            'organization_id' => $firstOrganization->id,
            'email' => 'shared@example.com',
        ]);

        $allowedDuplicate = Contact::factory()->create([
            'organization_id' => $secondOrganization->id,
            'email' => 'shared@example.com',
        ]);

        $this->assertTrue($allowedDuplicate->organization->is($secondOrganization));
    }
}
