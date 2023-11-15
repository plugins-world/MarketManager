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
        Schema::table('plugins', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_standalone')->default(0)->after('settings_path');

            $table->dropColumn('theme_functions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->unsignedTinyInteger('theme_functions')->default(0)->after('settings_path');
            $table->dropColumn('is_standalone');
        });
    }
};
