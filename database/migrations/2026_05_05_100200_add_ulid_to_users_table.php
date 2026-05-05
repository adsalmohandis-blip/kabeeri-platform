<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->ulid('ulid')->nullable()->unique()->after('id');
        });

        $users = DB::table('users')->whereNull('ulid')->get(['id']);

        foreach ($users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['ulid' => (string) Str::ulid()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};
