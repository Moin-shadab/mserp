<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create system_settings table if it doesn't exist
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            // Seed default theme setting
            DB::table('system_settings')->insert([
                'key' => 'default_theme',
                'value' => 'classic',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Add theme column to users table if it doesn't exist
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'theme')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('theme', 50)->default('classic')->after('can_send_to_anyone');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'theme')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('theme');
            });
        }

        Schema::dropIfExists('system_settings');
    }
};
