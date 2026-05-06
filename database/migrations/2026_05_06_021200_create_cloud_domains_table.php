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
        Schema::create('cloud_domains', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('cloud_site_id')->nullable()->constrained('cloud_sites')->nullOnDelete();
            $table->string('domain');
            $table->string('domain_type', 40)->default('custom');
            $table->string('verification_status', 40)->default('pending');
            $table->string('dns_status', 40)->default('unknown');
            $table->string('ssl_status', 40)->default('not_requested');
            $table->boolean('is_primary')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->json('dns_records')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'domain']);
            $table->index(['site_id', 'is_primary']);
            $table->index(['organization_id', 'verification_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_domains');
    }
};
