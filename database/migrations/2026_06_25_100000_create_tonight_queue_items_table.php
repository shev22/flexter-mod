<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->timestamp('tonight_queue_started_at')->nullable()->after('favorite_genre_ids');
        });

        Schema::create('tonight_queue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('media_type', 10);
            $table->unsignedBigInteger('media_id');
            $table->string('title');
            $table->string('poster_path')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'media_type', 'media_id']);
        });

        if (Schema::hasTable('users')) {
            \Illuminate\Support\Facades\DB::table('users')
                ->whereNull('email_verified_at')
                ->update(['email_verified_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tonight_queue_items');

        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn('tonight_queue_started_at');
        });
    }
};
