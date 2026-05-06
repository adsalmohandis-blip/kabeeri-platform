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
        Schema::create('inventory_items', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('item_type', 40)->default('stocked');
            $table->string('unit_of_measure', 20)->default('pcs');
            $table->string('status', 30)->default('active');
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->decimal('reorder_level', 12, 2)->nullable();
            $table->decimal('current_quantity', 12, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'sku']);
            $table->index('organization_id');
            $table->index('company_id');
            $table->index('site_id');
            $table->index('product_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
