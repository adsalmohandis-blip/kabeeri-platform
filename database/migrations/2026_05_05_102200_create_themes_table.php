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
        Schema::create('themes', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('version')->nullable();
            $table->string('publisher')->nullable();
            $table->string('status', 30)->default('active');
            $table->string('type', 30)->default('official');
            $table->boolean('supports_rtl')->default(true);
            $table->boolean('supports_dark_mode')->default(false);
            $table->foreignId('preview_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->json('manifest')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
