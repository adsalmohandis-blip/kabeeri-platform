<?php

namespace Tests\Feature;

use App\Filament\Resources\Contacts\ContactResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentCrmResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_access_crm_resource_indexes(): void
    {
        $owner = User::factory()->create();
        Organization::factory()->create(['owner_user_id' => $owner->id]);

        $this->actingAs($owner);

        $this->get(route('filament.admin.resources.contacts.index'))->assertOk();
        $this->get(route('filament.admin.resources.leads.index'))->assertOk();
        $this->get(route('filament.admin.resources.service-requests.index'))->assertOk();
    }

    public function test_crm_resource_queries_are_tenant_scoped(): void
    {
        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_user_id' => $owner->id]);
        $visibleContact = Contact::factory()->create(['organization_id' => $organization->id]);
        $hiddenContact = Contact::factory()->create();
        $visibleLead = Lead::factory()->create(['organization_id' => $organization->id]);
        $hiddenLead = Lead::factory()->create();
        $visibleRequest = ServiceRequest::factory()->create(['organization_id' => $organization->id]);
        $hiddenRequest = ServiceRequest::factory()->create();

        $this->actingAs($owner);

        $this->assertTrue(ContactResource::getEloquentQuery()->whereKey($visibleContact->id)->exists());
        $this->assertFalse(ContactResource::getEloquentQuery()->whereKey($hiddenContact->id)->exists());
        $this->assertTrue(LeadResource::getEloquentQuery()->whereKey($visibleLead->id)->exists());
        $this->assertFalse(LeadResource::getEloquentQuery()->whereKey($hiddenLead->id)->exists());
        $this->assertTrue(ServiceRequestResource::getEloquentQuery()->whereKey($visibleRequest->id)->exists());
        $this->assertFalse(ServiceRequestResource::getEloquentQuery()->whereKey($hiddenRequest->id)->exists());
    }
}
