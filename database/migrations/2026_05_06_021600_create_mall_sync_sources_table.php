<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mall_sync_sources', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('external_source_id')->nullable()->constrained('external_sources')->nullOnDelete();
            $table->string('source_name');
            $table->string('source_type', 60)->default('manual');
            $table->string('sync_scope', 60)->default('business_directory');
            $table->string('sync_direction', 60)->default('mirror_to_mall');
            $table->string('status', 40)->default('draft');
            $table->timestamp('last_preview_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['site_id', 'sync_scope']);
            $table->index('external_source_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mall_sync_sources');
    }
};
