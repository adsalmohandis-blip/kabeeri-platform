<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desktop_clients', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('client_uuid')->unique();
            $table->string('name')->nullable();
            $table->string('platform', 40)->default('unknown');
            $table->string('app_version', 40)->nullable();
            $table->string('status', 40)->default('active');
            $table->timestamp('last_seen_at')->nullable();
            $table->json('capabilities')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('desktop_sync_sessions', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('desktop_client_id')->constrained('desktop_clients')->cascadeOnDelete();
            $table->string('session_uuid')->unique();
            $table->string('direction', 40)->default('bidirectional');
            $table->string('status', 40)->default('active');
            $table->string('cursor')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_pull_at')->nullable();
            $table->timestamp('last_push_dry_run_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('summary')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['desktop_client_id', 'status']);
        });

        Schema::create('desktop_outbox_operations', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('desktop_sync_session_id')->constrained('desktop_sync_sessions')->cascadeOnDelete();
            $table->string('client_operation_id');
            $table->string('operation_type', 40);
            $table->string('entity_type', 120);
            $table->string('entity_id')->nullable();
            $table->unsignedInteger('base_version')->nullable();
            $table->unsignedInteger('server_version')->nullable();
            $table->string('status', 40)->default('accepted_dry_run');
            $table->json('payload_preview')->nullable();
            $table->json('validation_errors')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();

            $table->unique(['desktop_sync_session_id', 'client_operation_id']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['status']);
        });

        Schema::create('desktop_sync_conflicts', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('desktop_sync_session_id')->constrained('desktop_sync_sessions')->cascadeOnDelete();
            $table->foreignId('desktop_outbox_operation_id')->nullable()->constrained('desktop_outbox_operations')->nullOnDelete();
            $table->string('entity_type', 120);
            $table->string('entity_id')->nullable();
            $table->string('conflict_type', 80)->default('version_mismatch');
            $table->string('status', 40)->default('open');
            $table->json('client_snapshot')->nullable();
            $table->json('server_snapshot')->nullable();
            $table->json('resolution')->nullable();
            $table->timestamps();

            $table->index(['status', 'conflict_type']);
        });

        Schema::create('desktop_file_queue_items', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('desktop_client_id')->constrained('desktop_clients')->cascadeOnDelete();
            $table->foreignId('desktop_sync_session_id')->nullable()->constrained('desktop_sync_sessions')->nullOnDelete();
            $table->string('queue_uuid')->unique();
            $table->string('direction', 40)->default('upload');
            $table->string('filename');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('sha256')->nullable();
            $table->string('status', 40)->default('queued');
            $table->string('storage_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['desktop_client_id', 'status']);
            $table->index(['sha256']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desktop_file_queue_items');
        Schema::dropIfExists('desktop_sync_conflicts');
        Schema::dropIfExists('desktop_outbox_operations');
        Schema::dropIfExists('desktop_sync_sessions');
        Schema::dropIfExists('desktop_clients');
    }
};
