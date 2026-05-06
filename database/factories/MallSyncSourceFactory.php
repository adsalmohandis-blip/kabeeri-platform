<?php

namespace Database\Factories;

use App\Models\ExternalSource;
use App\Models\MallSyncSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MallSyncSource>
 */
class MallSyncSourceFactory extends Factory
{
    protected $model = MallSyncSource::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $externalSource = ExternalSource::factory()->create();

        return [
            'organization_id' => $externalSource->organization_id,
            'site_id' => $externalSource->site_id,
            'external_source_id' => $externalSource->id,
            'source_name' => $externalSource->source_name,
            'source_type' => $externalSource->source_type,
            'sync_scope' => 'business_directory',
            'sync_direction' => 'mirror_to_mall',
            'status' => 'draft',
            'last_preview_at' => null,
            'last_synced_at' => null,
            'settings' => ['mode' => 'preview_first'],
            'metadata' => ['source' => 'factory'],
        ];
    }
}
