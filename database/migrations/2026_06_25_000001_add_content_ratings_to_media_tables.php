<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->boolean('adult')->default(false)->after('category');
            $table->string('certification', 16)->nullable()->after('adult');
        });

        Schema::table('tv', function (Blueprint $table) {
            $table->boolean('adult')->default(false)->after('category');
            $table->string('certification', 16)->nullable()->after('adult');
        });
    }

    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn(['adult', 'certification']);
        });

        Schema::table('tv', function (Blueprint $table) {
            $table->dropColumn(['adult', 'certification']);
        });
    }
};
