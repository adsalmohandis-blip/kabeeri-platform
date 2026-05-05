<?php

namespace Database\Factories;

use App\Models\ContentEntry;
use App\Models\ContentRevision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentRevision>
 */
class ContentRevisionFactory extends Factory
{
    protected $model = ContentRevision::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content_entry_id' => ContentEntry::factory(),
            'created_by' => fake()->boolean(70) ? User::factory() : null,
            'title' => fake()->optional()->sentence(4),
            'body' => fake()->optional(80)->paragraphs(2, true),
            'data' => ['source' => 'factory'],
            'created_at' => now(),
        ];
    }
}
