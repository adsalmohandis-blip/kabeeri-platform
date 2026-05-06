<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('tier', 40)->default('free');
            $table->unsignedInteger('price_cents')->default(0);
            $table->string('currency_code', 3)->default('USD');
            $table->string('billing_interval', 30)->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('tier');
            $table->index(['is_public', 'is_active']);
            $table->index('sort_order');
        });

        Schema::create('plan_entitlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->string('key');
            $table->string('value_type', 30)->default('integer');
            $table->bigInteger('limit_value')->nullable();
            $table->boolean('bool_value')->nullable();
            $table->string('string_value')->nullable();
            $table->string('reset_period', 30)->nullable();
            $table->string('behavior', 30)->default('allow');
            $table->string('upgrade_plan_code')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'key']);
            $table->index('key');
            $table->index('behavior');
        });

        Schema::create('usage_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('entitlement_key');
            $table->unsignedBigInteger('quantity')->default(0);
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('organization_id');
            $table->index('site_id');
            $table->index('user_id');
            $table->index('entitlement_key');
            $table->index(['source_type', 'source_id']);
            $table->index(['period_start', 'period_end']);
        });

        Schema::create('entitlement_overrides', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->cascadeOnDelete();
            $table->string('key');
            $table->string('value_type', 30)->default('integer');
            $table->bigInteger('limit_value')->nullable();
            $table->boolean('bool_value')->nullable();
            $table->string('string_value')->nullable();
            $table->string('behavior', 30)->default('allow');
            $table->text('reason')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('organization_id');
            $table->index('site_id');
            $table->index('key');
            $table->index('behavior');
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entitlement_overrides');
        Schema::dropIfExists('usage_records');
        Schema::dropIfExists('plan_entitlements');
        Schema::dropIfExists('plans');
    }
};
