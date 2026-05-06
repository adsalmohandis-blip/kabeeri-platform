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
        Schema::create('partner_catalog_shares', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('partner_storefront_id')->constrained('partner_storefronts')->cascadeOnDelete();
            $table->nullableMorphs('catalogable');
            $table->string('share_type', 60)->default('catalog_item');
            $table->string('status', 40)->default('draft');
            $table->string('visibility', 40)->default('private');
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['partner_storefront_id', 'status'], 'partner_catalog_shares_storefront_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_catalog_shares');
    }
};
