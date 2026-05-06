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
        Schema::create('legal_partner_profiles', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('display_name');
            $table->string('slug');
            $table->string('partner_type', 60)->default('legal_consultant');
            $table->json('practice_areas')->nullable();
            $table->json('jurisdictions')->nullable();
            $table->json('languages')->nullable();
            $table->json('contact_channels')->nullable();
            $table->string('verification_status', 40)->default('not_submitted');
            $table->string('network_status', 40)->default('draft');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'slug']);
            $table->index(['organization_id', 'network_status']);
            $table->index(['company_id', 'network_status']);
            $table->index(['verification_status', 'network_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_partner_profiles');
    }
};
