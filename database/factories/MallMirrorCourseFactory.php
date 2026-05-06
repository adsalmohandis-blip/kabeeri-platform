<?php

namespace Database\Factories;

use App\Models\MallMirrorCourse;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MallMirrorCourse> */
class MallMirrorCourseFactory extends Factory
{
    protected $model = MallMirrorCourse::class;

    public function definition(): array
    {
        $courseName = fake()->words(4, true);

        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'site_id' => null,
            'course_id' => null,
            'mall_publication_consent_id' => null,
            'course_name' => $courseName,
            'slug' => str()->slug($courseName).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->optional()->text(200),
            'training_type' => fake()->randomElement(['course', 'workshop', 'bootcamp', 'webinar']),
            'delivery_mode' => fake()->randomElement(['online', 'onsite', 'hybrid']),
            'price' => fake()->randomFloat(2, 0, 2000),
            'currency' => 'USD',
            'schedule' => ['duration_hours' => fake()->numberBetween(1, 80)],
            'instructor_info' => ['name' => fake()->name()],
            'images' => ['image_url' => fake()->imageUrl()],
            'mirror_status' => 'draft',
            'published_at' => null,
            'last_refreshed_at' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
