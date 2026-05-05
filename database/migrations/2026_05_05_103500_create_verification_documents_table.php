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
        Schema::create('verification_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('verification_request_id')->constrained('verification_requests')->cascadeOnDelete();
            $table->foreignId('media_asset_id')->constrained('media_assets')->cascadeOnDelete();
            $table->string('document_type');
            $table->string('status', 30)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('verification_request_id');
            $table->index('media_asset_id');
            $table->index('document_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_documents');
    }
};
