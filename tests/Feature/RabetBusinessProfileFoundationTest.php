<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\Company;
use App\Models\MediaAsset;
use App\Models\Organization;
use App\Models\User;
use App\Models\VerificationDocument;
use App\Models\VerificationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RabetBusinessProfileFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_have_business_profile_draft(): void
    {
        $organization = Organization::factory()->create();
        $company = Company::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $profile = BusinessProfile::factory()->create([
            'organization_id' => $organization->id,
            'company_id' => $company->id,
            'display_name' => 'Demo Business Profile',
            'slug' => 'demo-business-profile',
            'visibility' => 'draft',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('business_profiles', [
            'id' => $profile->id,
            'organization_id' => $organization->id,
            'company_id' => $company->id,
            'status' => 'draft',
            'visibility' => 'draft',
        ]);
    }

    public function test_verification_request_can_be_created_as_draft_and_submitted(): void
    {
        $requester = User::factory()->create();
        $organization = Organization::factory()->create();
        $company = Company::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $verificationRequest = VerificationRequest::factory()->create([
            'organization_id' => $organization->id,
            'company_id' => $company->id,
            'requested_by' => $requester->id,
            'status' => 'draft',
            'submitted_at' => null,
        ]);

        $this->assertDatabaseHas('verification_requests', [
            'id' => $verificationRequest->id,
            'status' => 'draft',
        ]);

        $verificationRequest->forceFill([
            'status' => 'submitted',
            'submitted_at' => now(),
        ])->save();

        $this->assertDatabaseHas('verification_requests', [
            'id' => $verificationRequest->id,
            'status' => 'submitted',
        ]);
        $this->assertNotNull($verificationRequest->fresh()->submitted_at);
    }

    public function test_verification_documents_can_be_attached_to_request(): void
    {
        $requester = User::factory()->create();
        $organization = Organization::factory()->create();
        $company = Company::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $verificationRequest = VerificationRequest::factory()->create([
            'organization_id' => $organization->id,
            'company_id' => $company->id,
            'requested_by' => $requester->id,
        ]);

        $media = MediaAsset::query()->create([
            'organization_id' => $organization->id,
            'company_id' => $company->id,
            'site_id' => null,
            'uploaded_by' => $requester->id,
            'disk' => 'private',
            'path' => 'organizations/'.$organization->id.'/verification/cr.pdf',
            'relative_path' => 'verification/cr.pdf',
            'filename' => 'cr.pdf',
            'original_filename' => 'commercial-registration.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size_bytes' => 100000,
            'width' => null,
            'height' => null,
            'visibility' => 'private',
            'alt_text' => null,
            'caption' => null,
            'checksum' => 'doc-checksum-001',
            'metadata' => ['source' => 'test'],
        ]);

        $document = VerificationDocument::factory()->create([
            'verification_request_id' => $verificationRequest->id,
            'media_asset_id' => $media->id,
            'document_type' => 'commercial_registration',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('verification_documents', [
            'id' => $document->id,
            'verification_request_id' => $verificationRequest->id,
            'media_asset_id' => $media->id,
            'document_type' => 'commercial_registration',
        ]);
    }
}
