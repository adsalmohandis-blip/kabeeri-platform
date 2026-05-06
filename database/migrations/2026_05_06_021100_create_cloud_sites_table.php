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
        Schema::create('cloud_sites', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('environment', 40)->default('production');
            $table->string('provider', 80)->nullable();
            $table->string('region', 80)->nullable();
            $table->string('deployment_status', 40)->default('draft');
            $table->string('health_status', 40)->default('unknown');
            $table->string('public_url')->nullable();
            $table->timestamp('last_deployed_at')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['site_id', 'environment']);
            $table->index(['organization_id', 'deployment_status']);
            $table->index(['organization_id', 'health_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_sites');
    }
};
