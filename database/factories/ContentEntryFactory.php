<?php

namespace Database\Factories;

use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ContentEntry>
 */
class ContentEntryFactory extends Factory
{
    protected $model = ContentEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'organization_id' => Organization::factory(),
            'site_id' => Site::factory()->state(fn (array $attributes): array => [
                'organization_id' => $attributes['organization_id'],
            ]),
            'content_type_id' => ContentType::factory()->state(fn (array $attributes): array => [
                'organization_id' => $attributes['organization_id'],
                'site_id' => $attributes['site_id'],
            ]),
            'author_user_id' => fake()->boolean(70) ? User::factory() : null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'excerpt' => fake()->optional()->text(140),
            'body' => fake()->optional(80)->paragraphs(2, true),
            'status' => 'draft',
            'visibility' => 'public',
            'published_at' => null,
            'seo' => [
                'title' => Str::limit($title, 60, ''),
                'description' => fake()->sentence(),
            ],
            'seo_title' => null,
            'seo_description' => null,
            'canonical_url' => null,
            'og_title' => null,
            'og_description' => null,
            'og_image_media_id' => null,
            'noindex' => false,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
