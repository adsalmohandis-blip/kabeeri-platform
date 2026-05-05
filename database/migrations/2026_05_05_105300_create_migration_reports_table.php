<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migration_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('import_job_id')->constrained('import_jobs')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->cascadeOnDelete();
            $table->string('status', 30)->default('generated');
            $table->json('data');
            $table->timestamps();

            $table->index('import_job_id');
            $table->index('organization_id');
            $table->index('site_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migration_reports');
    }
};
