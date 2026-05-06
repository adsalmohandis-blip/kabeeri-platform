<?php

namespace Tests\Feature;

use App\Models\MallMirrorCourse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MallCoursesPublicBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_courses_page_lists_published_courses_only(): void
    {
        MallMirrorCourse::factory()->create([
            'course_name' => 'Published Course',
            'slug' => 'published-course',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);
        MallMirrorCourse::factory()->create([
            'course_name' => 'Draft Course',
            'slug' => 'draft-course',
            'mirror_status' => 'draft',
        ]);

        $this->get('/mall/courses')
            ->assertOk()
            ->assertSee('Published Course')
            ->assertDontSee('Draft Course');
    }

    public function test_course_detail_renders_published_course(): void
    {
        $course = MallMirrorCourse::factory()->create([
            'course_name' => 'Public Course',
            'slug' => 'public-course',
            'description' => 'A published course.',
            'training_type' => 'workshop',
            'delivery_mode' => 'online',
            'price' => 200,
            'currency' => 'USD',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/mall/courses/'.$course->slug)
            ->assertOk()
            ->assertSee('Public Course')
            ->assertSee('A published course.')
            ->assertSee('workshop')
            ->assertSee('online')
            ->assertSee('USD 200.00');
    }

    public function test_course_detail_hides_unpublished_course(): void
    {
        $course = MallMirrorCourse::factory()->create([
            'slug' => 'hidden-course',
            'mirror_status' => 'needs_review',
        ]);

        $this->get('/mall/courses/'.$course->slug)
            ->assertNotFound();
    }
}
