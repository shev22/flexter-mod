<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->boolean('allow_adult')->default(false)->after('subtitles');
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn('maturity');
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->string('maturity')->default('all')->after('subtitles');
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn('allow_adult');
        });
    }
};
