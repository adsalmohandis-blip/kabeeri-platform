<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('import_job_id')->constrained('import_jobs')->cascadeOnDelete();
            $table->string('source_entity_type');
            $table->string('source_entity_id')->nullable();
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('status', 30)->default('pending');
            $table->json('warnings')->nullable();
            $table->json('errors')->nullable();
            $table->json('source_payload')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('import_job_id');
            $table->index('source_entity_type');
            $table->index('source_entity_id');
            $table->index(['target_type', 'target_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_records');
    }
};
