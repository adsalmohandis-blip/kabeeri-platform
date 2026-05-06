<?php

namespace Tests\Feature;

use App\Models\EmployeeProfile;
use App\Models\MallPublicationConsent;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Mall\Services\MallMirrorTalentService;
use App\Modules\Mall\Services\MallPublicationConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MallMirrorTalentTest extends TestCase
{
    use RefreshDatabase;

    public function test_talent_profile_can_be_mirrored_as_draft(): void
    {
        $organization = Organization::factory()->create();
        $employee = EmployeeProfile::factory()->create([
            'organization_id' => $organization->id,
            'full_name' => 'Mona Designer',
        ]);

        $talent = app(MallMirrorTalentService::class)->createDraft($organization, [
            'display_name' => 'Mona Designer',
            'slug' => 'mona-designer',
            'headline' => 'Product Designer',
            'skills' => ['design', 'research'],
        ], $employee);

        $this->assertSame($employee->id, $talent->employee_profile_id);
        $this->assertSame('draft', $talent->mirror_status);
        $this->assertSame(['design', 'research'], $talent->skills);
        $this->assertTrue($organization->mallMirrorTalent()->whereKey($talent->id)->exists());
    }

    public function test_talent_publication_requires_granted_talent_consent(): void
    {
        $organization = Organization::factory()->create();
        $service = app(MallMirrorTalentService::class);
        $talent = $service->createDraft($organization, [
            'display_name' => 'Omar Developer',
            'slug' => 'omar-developer',
        ]);

        $this->expectValidationFailure(fn () => $service->publish($talent));

        $consent = app(MallPublicationConsentService::class)->request($organization, channels: ['talent']);
        app(MallPublicationConsentService::class)->grant($consent, User::factory()->create());

        $talent = $service->createDraft($organization, [
            'display_name' => 'Omar Developer',
            'slug' => 'omar-developer',
        ], consent: $consent);

        $published = $service->publish($talent);

        $this->assertSame('published', $published->mirror_status);
        $this->assertNotNull($published->published_at);
    }

    public function test_talent_mirror_rejects_cross_tenant_employee_or_consent(): void
    {
        $organization = Organization::factory()->create();
        $foreignEmployee = EmployeeProfile::factory()->create();
        $foreignConsent = MallPublicationConsent::factory()->create();
        $service = app(MallMirrorTalentService::class);

        $this->expectValidationFailure(fn () => $service->createDraft($organization, [
            'display_name' => 'Foreign Employee',
            'slug' => 'foreign-employee',
        ], $foreignEmployee));

        $this->expectValidationFailure(fn () => $service->createDraft($organization, [
            'display_name' => 'Foreign Consent',
            'slug' => 'foreign-consent',
        ], consent: $foreignConsent));
    }

    private function expectValidationFailure(callable $callback): void
    {
        try {
            $callback();
        } catch (ValidationException) {
            $this->addToAssertionCount(1);

            return;
        }

        $this->fail('Expected validation exception.');
    }
}
