<?php

namespace Database\Factories;

use App\Models\CloudBackup;
use App\Models\CloudSite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CloudBackup>
 */
class CloudBackupFactory extends Factory
{
    protected $model = CloudBackup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cloudSite = CloudSite::factory()->create();

        return [
            'organization_id' => $cloudSite->organization_id,
            'site_id' => $cloudSite->site_id,
            'cloud_site_id' => $cloudSite->id,
            'backup_type' => 'manual',
            'status' => 'pending',
            'storage_label' => 'local-reference',
            'backup_reference' => null,
            'size_bytes' => null,
            'started_at' => null,
            'completed_at' => null,
            'failed_at' => null,
            'errors' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
