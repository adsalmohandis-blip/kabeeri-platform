<?php

namespace Database\Factories;

use App\Models\MallSyncEvent;
use App\Models\MallSyncSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MallSyncEvent>
 */
class MallSyncEventFactory extends Factory
{
    protected $model = MallSyncEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $source = MallSyncSource::factory()->create();

        return [
            'organization_id' => $source->organization_id,
            'mall_sync_source_id' => $source->id,
            'event_type' => 'preview',
            'status' => 'pending',
            'total_records' => 0,
            'processed_records' => 0,
            'failed_records' => 0,
            'warnings' => null,
            'errors' => null,
            'metadata' => ['source' => 'factory'],
            'started_at' => now(),
            'completed_at' => null,
        ];
    }
}
