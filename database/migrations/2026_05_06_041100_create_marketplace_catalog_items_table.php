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
        Schema::create('marketplace_catalog_items', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->nullableMorphs('catalogable');
            $table->string('item_kind', 40);
            $table->string('listing_status', 40)->default('draft');
            $table->string('visibility', 40)->default('internal');
            $table->string('governance_status', 40)->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('submitted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['catalogable_type', 'catalogable_id'], 'marketplace_catalog_items_catalogable_unique');
            $table->index(['item_kind', 'listing_status']);
            $table->index(['visibility', 'governance_status']);
            $table->index(['is_featured', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_catalog_items');
    }
};
