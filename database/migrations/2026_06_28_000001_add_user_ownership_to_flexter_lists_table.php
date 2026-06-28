<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flexter_lists', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->string('visibility', 16)->default('private')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('flexter_lists', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('visibility');
        });
    }
};
