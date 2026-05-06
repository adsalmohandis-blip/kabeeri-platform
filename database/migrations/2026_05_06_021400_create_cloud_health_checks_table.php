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
        Schema::create('cloud_health_checks', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('cloud_site_id')->nullable()->constrained('cloud_sites')->nullOnDelete();
            $table->string('check_type', 60)->default('manual');
            $table->string('status', 40)->default('unknown');
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->text('message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['site_id', 'checked_at']);
            $table->index(['cloud_site_id', 'check_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_health_checks');
    }
};
