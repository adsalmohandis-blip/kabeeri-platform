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
        Schema::table('content_entries', function (Blueprint $table): void {
            $table->string('seo_title')->nullable()->after('seo');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('canonical_url')->nullable()->after('seo_description');
            $table->string('og_title')->nullable()->after('canonical_url');
            $table->text('og_description')->nullable()->after('og_title');
            $table->foreignId('og_image_media_id')->nullable()->after('og_description')->constrained('media_assets')->nullOnDelete();
            $table->boolean('noindex')->default(false)->after('og_image_media_id');

            $table->index('og_image_media_id');
            $table->index('noindex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_entries', function (Blueprint $table): void {
            $table->dropForeign(['og_image_media_id']);
            $table->dropIndex(['og_image_media_id']);
            $table->dropIndex(['noindex']);
            $table->dropColumn([
                'seo_title',
                'seo_description',
                'canonical_url',
                'og_title',
                'og_description',
                'og_image_media_id',
                'noindex',
            ]);
        });
    }
};
