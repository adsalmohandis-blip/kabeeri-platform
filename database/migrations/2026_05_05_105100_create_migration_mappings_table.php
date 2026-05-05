<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migration_mappings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('import_job_id')->constrained('import_jobs')->cascadeOnDelete();
            $table->string('source_type');
            $table->string('source_id')->nullable();
            $table->string('source_key')->nullable();
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('mapping_status', 30)->default('pending');
            $table->string('mapping_strategy')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('import_job_id');
            $table->index('source_type');
            $table->index('source_id');
            $table->index('source_key');
            $table->index(['target_type', 'target_id']);
            $table->index('mapping_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migration_mappings');
    }
};
