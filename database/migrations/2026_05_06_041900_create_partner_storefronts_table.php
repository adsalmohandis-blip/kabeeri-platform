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
        Schema::create('partner_storefronts', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('agency_partner_profile_id')->nullable()->constrained('agency_partner_profiles')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('storefront_type', 60)->default('partner_catalog');
            $table->string('status', 40)->default('draft');
            $table->string('visibility', 40)->default('private');
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'slug']);
            $table->index(['organization_id', 'status']);
            $table->index(['agency_partner_profile_id', 'status'], 'partner_storefronts_agency_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_storefronts');
    }
};
