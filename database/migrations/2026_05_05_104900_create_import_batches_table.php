<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('import_job_id')->constrained('import_jobs')->cascadeOnDelete();
            $table->string('batch_type');
            $table->string('status', 30)->default('pending');
            $table->unsignedInteger('total_records')->default(0);
            $table->unsignedInteger('processed_records')->default(0);
            $table->unsignedInteger('failed_records')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('import_job_id');
            $table->index('batch_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
