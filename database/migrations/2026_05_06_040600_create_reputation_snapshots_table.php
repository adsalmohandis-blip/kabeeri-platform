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
        Schema::create('reputation_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->nullableMorphs('subject');
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('published_review_count')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->unsignedInteger('open_moderation_cases_count')->default(0);
            $table->unsignedTinyInteger('trust_score')->default(0);
            $table->timestamp('calculated_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['subject_type', 'subject_id'], 'reputation_snapshots_subject_unique');
            $table->index(['organization_id', 'trust_score']);
            $table->index(['site_id', 'trust_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reputation_snapshots');
    }
};
