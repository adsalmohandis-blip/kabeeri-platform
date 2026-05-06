<?php

namespace Tests\Feature;

use App\Models\MallMirrorCourse;
use App\Models\MallPublicationConsent;
use App\Models\Organization;
use App\Models\User;
use App\Modules\Mall\Services\MallMirrorCourseService;
use App\Modules\Mall\Services\MallPublicationConsentService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MallMirrorCoursesTest extends TestCase
{
    use RefreshDatabase;

    public function test_mall_mirror_course_can_be_created_as_draft(): void
    {
        $organization = Organization::factory()->create();

        $course = app(MallMirrorCourseService::class)->createDraft($organization, [
            'course_name' => 'Digital Marketing Basics',
            'slug' => 'digital-marketing-basics',
            'training_type' => 'course',
            'delivery_mode' => 'online',
            'price' => 250,
        ]);

        $this->assertSame('Digital Marketing Basics', $course->course_name);
        $this->assertSame('digital-marketing-basics', $course->slug);
        $this->assertSame('250.00', $course->price);
        $this->assertSame('draft', $course->mirror_status);
        $this->assertTrue($organization->mallMirrorCourses()->whereKey($course->id)->exists());
    }

    public function test_mall_mirror_course_requires_granted_courses_consent_before_publish(): void
    {
        $organization = Organization::factory()->create();
        $service = app(MallMirrorCourseService::class);
        $course = $service->createDraft($organization, [
            'course_name' => 'Leadership Workshop',
            'slug' => 'leadership-workshop',
        ]);

        $this->expectValidationFailure(fn () => $service->publish($course));

        $consent = app(MallPublicationConsentService::class)->request($organization, channels: ['courses']);
        app(MallPublicationConsentService::class)->grant($consent, User::factory()->create());

        $course = $service->createDraft($organization, [
            'course_name' => 'Leadership Workshop',
            'slug' => 'leadership-workshop',
        ], $consent);
        $published = $service->publish($course);

        $this->assertSame('published', $published->mirror_status);
        $this->assertNotNull($published->published_at);
    }

    public function test_mall_mirror_course_can_be_unpublished(): void
    {
        $organization = Organization::factory()->create();
        $consent = app(MallPublicationConsentService::class)->grant(
            app(MallPublicationConsentService::class)->request($organization, channels: ['courses']),
            User::factory()->create(),
        );
        $service = app(MallMirrorCourseService::class);
        $course = $service->publish($service->createDraft($organization, [
            'course_name' => 'Finance Bootcamp',
            'slug' => 'finance-bootcamp',
        ], $consent));

        $unpublished = $service->unpublish($course);

        $this->assertSame('unpublished', $unpublished->mirror_status);
        $this->assertNull($unpublished->published_at);
    }

    public function test_mall_mirror_course_rejects_cross_tenant_consent(): void
    {
        $organization = Organization::factory()->create();
        $foreignConsent = MallPublicationConsent::factory()->create([
            'organization_id' => Organization::factory()->create()->id,
        ]);

        $this->expectException(ValidationException::class);

        app(MallMirrorCourseService::class)->createDraft($organization, [
            'course_name' => 'Foreign Consent',
            'slug' => 'foreign-consent',
        ], $foreignConsent);
    }

    public function test_mall_mirror_course_slug_is_unique_per_organization(): void
    {
        $organization = Organization::factory()->create();

        MallMirrorCourse::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'unique-course',
        ]);

        $this->expectException(QueryException::class);

        MallMirrorCourse::factory()->create([
            'organization_id' => $organization->id,
            'slug' => 'unique-course',
        ]);
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
