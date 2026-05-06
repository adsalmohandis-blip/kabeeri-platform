<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_plans', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('plan_type', 60)->default('partner_referral');
            $table->string('status', 40)->default('draft');
            $table->decimal('default_rate', 8, 4)->default(0);
            $table->json('rules')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
        });

        Schema::create('commission_events', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('commission_plan_id')->nullable()->constrained('commission_plans')->nullOnDelete();
            $table->foreignId('partner_storefront_id')->nullable()->constrained('partner_storefronts')->nullOnDelete();
            $table->nullableMorphs('commissionable');
            $table->string('event_type', 60)->default('referral');
            $table->string('status', 40)->default('pending');
            $table->decimal('base_amount', 14, 2)->default(0);
            $table->decimal('commission_rate', 8, 4)->default(0);
            $table->decimal('commission_amount', 14, 2)->default(0);
            $table->string('currency_code', 3)->default('EGP');
            $table->timestamp('earned_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['partner_storefront_id', 'status']);
        });

        Schema::create('partner_payouts', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('partner_storefront_id')->nullable()->constrained('partner_storefronts')->nullOnDelete();
            $table->string('payout_number');
            $table->string('status', 40)->default('pending_review');
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('currency_code', 3)->default('EGP');
            $table->string('payout_method_reference')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('risk_checks')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'payout_number']);
            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_payouts');
        Schema::dropIfExists('commission_events');
        Schema::dropIfExists('commission_plans');
    }
};
