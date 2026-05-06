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
        Schema::create('growth_referrals', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('referrer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('referred_organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('referred_email')->nullable();
            $table->string('source', 60)->default('manual');
            $table->string('status', 40)->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['referrer_user_id', 'status']);
            $table->index(['referred_organization_id', 'status'], 'growth_referrals_referred_org_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('growth_referrals');
    }
};
