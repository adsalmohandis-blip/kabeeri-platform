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
        Schema::create('travel_tourism_mall_listings', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('mall_publication_consent_id')->nullable()->constrained('mall_publication_consents')->nullOnDelete();
            $table->string('listing_type', 80)->default('tour');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('destination')->nullable();
            $table->string('country_code', 3)->nullable();
            $table->decimal('price_from', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->json('availability')->nullable();
            $table->json('contact_channels')->nullable();
            $table->json('images')->nullable();
            $table->string('listing_status', 40)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('last_refreshed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'slug']);
            $table->index(['organization_id', 'listing_status']);
            $table->index(['listing_type', 'destination']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_tourism_mall_listings');
    }
};
