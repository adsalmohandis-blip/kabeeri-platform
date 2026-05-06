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
        Schema::create('trust_badge_awards', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('trust_badge_id')->constrained('trust_badges')->cascadeOnDelete();
            $table->nullableMorphs('subject');
            $table->foreignId('awarded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('active');
            $table->timestamp('awarded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['trust_badge_id', 'subject_type', 'subject_id'], 'trust_badge_awards_badge_subject_unique');
            $table->index(['organization_id', 'status']);
            $table->index(['site_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trust_badge_awards');
    }
};
