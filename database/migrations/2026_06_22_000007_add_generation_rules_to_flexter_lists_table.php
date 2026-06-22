<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flexter_lists', function (Blueprint $table) {
            $table->json('genre_ids')->nullable()->after('description');
            $table->string('media_type', 16)->default('movie')->after('genre_ids');
            $table->unsignedSmallInteger('item_limit')->default(20)->after('media_type');
            $table->decimal('min_rating', 3, 1)->nullable()->after('item_limit');
            $table->unsignedSmallInteger('min_year')->nullable()->after('min_rating');
        });
    }

    public function down(): void
    {
        Schema::table('flexter_lists', function (Blueprint $table) {
            $table->dropColumn(['genre_ids', 'media_type', 'item_limit', 'min_rating', 'min_year']);
        });
    }
};
