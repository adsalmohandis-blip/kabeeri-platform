<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_usage_records', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('module_key', 80);
            $table->string('meter_key', 120);
            $table->decimal('quantity', 14, 4)->default(0);
            $table->string('unit', 40)->default('count');
            $table->date('usage_date');
            $table->nullableMorphs('billable');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'module_key', 'usage_date'], 'billing_usage_org_module_date_idx');
            $table->index(['meter_key', 'usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_usage_records');
    }
};
