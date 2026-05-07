<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->prepareExistingSites();

        Schema::table('sites', function (Blueprint $table): void {
            $table->dropUnique('sites_organization_id_slug_unique');
        });

        Schema::table('sites', function (Blueprint $table): void {
            $table->unique('slug', 'sites_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table): void {
            $table->dropUnique('sites_slug_unique');
        });

        Schema::table('sites', function (Blueprint $table): void {
            $table->unique(['organization_id', 'slug']);
        });
    }

    private function prepareExistingSites(): void
    {
        $seen = [];
        $now = now();

        DB::table('sites')
            ->select(['id', 'slug', 'metadata'])
            ->orderBy('id')
            ->get()
            ->each(function (object $site) use (&$seen, $now): void {
                $base = trim((string) $site->slug);
                $base = $base !== '' ? $base : 'app';
                $candidate = $base;
                $suffix = 2;

                while (isset($seen[$candidate])) {
                    $candidate = "{$base}-{$suffix}";
                    $suffix++;
                }

                $metadata = $this->decodeMetadata($site->metadata);
                $metadata['fixed_permalink_username'] = $candidate;
                $metadata['fixed_permalink_locked_at'] ??= $now->toISOString();

                $updates = [
                    'metadata' => json_encode($metadata, JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                ];

                if ($candidate !== (string) $site->slug) {
                    $updates['slug'] = $candidate;
                }

                DB::table('sites')
                    ->where('id', $site->id)
                    ->update($updates);

                $seen[$candidate] = true;
            });
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeMetadata(mixed $metadata): array
    {
        if (is_array($metadata)) {
            return $metadata;
        }

        if (is_object($metadata)) {
            return (array) $metadata;
        }

        if (is_string($metadata) && $metadata !== '') {
            $decoded = json_decode($metadata, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
};
