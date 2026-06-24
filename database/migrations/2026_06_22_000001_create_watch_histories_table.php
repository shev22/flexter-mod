<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('media_type', 16);
            $table->unsignedBigInteger('media_id');
            $table->unsignedSmallInteger('season_number')->nullable();
            $table->unsignedSmallInteger('episode_number')->nullable();
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamp('last_watched_at');
            $table->timestamps();

            $table->unique(
                ['user_id', 'media_type', 'media_id', 'season_number', 'episode_number'],
                'watch_histories_unique_entry',
            );
            $table->index(['user_id', 'last_watched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};
