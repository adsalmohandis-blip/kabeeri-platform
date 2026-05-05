<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('themes', function (Blueprint $table): void {
            $table->string('category')->nullable()->after('type');
            $table->json('industries')->nullable()->after('category');
            $table->json('app_types')->nullable()->after('industries');
            $table->string('price_type', 30)->default('free')->after('app_types');
            $table->string('demo_url')->nullable()->after('price_type');
            $table->json('preview_images')->nullable()->after('demo_url');
            $table->unsignedSmallInteger('performance_score')->nullable()->after('preview_images');
            $table->json('compatibility')->nullable()->after('performance_score');

            $table->index('category');
            $table->index('price_type');
            $table->index('performance_score');
        });
    }

    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table): void {
            $table->dropIndex(['category']);
            $table->dropIndex(['price_type']);
            $table->dropIndex(['performance_score']);
            $table->dropColumn([
                'category',
                'industries',
                'app_types',
                'price_type',
                'demo_url',
                'preview_images',
                'performance_score',
                'compatibility',
            ]);
        });
    }
};
