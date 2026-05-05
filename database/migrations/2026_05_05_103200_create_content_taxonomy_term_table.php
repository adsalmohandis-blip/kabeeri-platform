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
        Schema::create('content_taxonomy_term', function (Blueprint $table): void {
            $table->foreignId('content_entry_id')->constrained('content_entries')->cascadeOnDelete();
            $table->foreignId('taxonomy_term_id')->constrained('taxonomy_terms')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['content_entry_id', 'taxonomy_term_id'], 'content_taxonomy_term_primary');
            $table->index('taxonomy_term_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_taxonomy_term');
    }
};
