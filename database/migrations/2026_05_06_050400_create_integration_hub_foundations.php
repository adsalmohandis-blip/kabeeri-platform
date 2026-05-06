<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_connectors', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->string('provider', 80);
            $table->string('connector_type', 80)->default('api');
            $table->string('status', 40)->default('draft');
            $table->json('capabilities')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'key']);
            $table->index(['provider', 'status']);
        });

        Schema::create('integration_credentials', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_connector_id')->constrained('integration_connectors')->cascadeOnDelete();
            $table->string('credential_type', 80)->default('api_key_reference');
            $table->string('vault_reference');
            $table->string('status', 40)->default('inactive');
            $table->timestamp('last_verified_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'integration_connector_id', 'credential_type'], 'integration_credentials_unique_type');
            $table->index(['organization_id', 'status']);
        });

        Schema::create('external_object_links', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_connector_id')->constrained('integration_connectors')->cascadeOnDelete();
            $table->nullableMorphs('linkable');
            $table->string('external_object_type', 120);
            $table->string('external_object_id');
            $table->string('sync_direction', 40)->default('preview');
            $table->string('status', 40)->default('linked');
            $table->timestamp('last_seen_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['integration_connector_id', 'external_object_type', 'external_object_id'], 'external_object_links_unique_external');
            $table->index(['organization_id', 'status']);
        });

        Schema::create('integration_sync_jobs', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_connector_id')->constrained('integration_connectors')->cascadeOnDelete();
            $table->string('job_type', 80)->default('preview_import');
            $table->string('status', 40)->default('queued');
            $table->string('direction', 40)->default('import');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->json('payload')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status', 'scheduled_at']);
            $table->index(['integration_connector_id', 'status']);
        });

        Schema::create('integration_sync_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('integration_sync_job_id')->nullable()->constrained('integration_sync_jobs')->nullOnDelete();
            $table->foreignId('integration_connector_id')->nullable()->constrained('integration_connectors')->nullOnDelete();
            $table->string('level', 40)->default('info');
            $table->string('message');
            $table->string('error_code', 80)->nullable();
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_sync_logs');
        Schema::dropIfExists('integration_sync_jobs');
        Schema::dropIfExists('external_object_links');
        Schema::dropIfExists('integration_credentials');
        Schema::dropIfExists('integration_connectors');
    }
};
