<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('package_type');
            $table->string('publisher_type')->default('official');
            $table->string('status', 30)->default('active');
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->json('permissions')->nullable();
            $table->json('dependencies')->nullable();
            $table->json('compatibility')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('package_type');
            $table->index('publisher_type');
            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
