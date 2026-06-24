<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flexter_lists', function (Blueprint $table) {
            $table->string('icon', 32)->default('film')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('flexter_lists', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
