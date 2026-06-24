<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->string('density')->default('comfortable')->after('maturity');
            $table->boolean('high_contrast')->default(false)->after('density');
            $table->string('language', 8)->default('en')->after('high_contrast');
            $table->boolean('email_notifications')->default(true)->after('language');
            $table->boolean('spoiler_free')->default(false)->after('email_notifications');
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn([
                'density',
                'high_contrast',
                'language',
                'email_notifications',
                'spoiler_free',
            ]);
        });
    }
};
