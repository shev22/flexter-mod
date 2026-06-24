<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->index(['category', 'is_trending']);
            $table->index('is_trending');
            $table->index('release_date');
        });

        Schema::table('tv', function (Blueprint $table) {
            $table->index(['category', 'is_trending']);
            $table->index('is_trending');
            $table->index('release_date');
        });

        Schema::table('actors', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('watch_lists', function (Blueprint $table) {
            $table->unique(['user_id', 'media_type', 'media_id']);
            $table->index(['user_id', 'media_type']);
        });

        Schema::table('watch_histories', function (Blueprint $table) {
            $table->index(['user_id', 'completed', 'last_watched_at']);
            $table->index(['user_id', 'media_type', 'media_id']);
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->index('read_at');
        });
    }

    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropIndex(['category', 'is_trending']);
            $table->dropIndex(['is_trending']);
            $table->dropIndex(['release_date']);
        });

        Schema::table('tv', function (Blueprint $table) {
            $table->dropIndex(['category', 'is_trending']);
            $table->dropIndex(['is_trending']);
            $table->dropIndex(['release_date']);
        });

        Schema::table('actors', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('watch_lists', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'media_type', 'media_id']);
            $table->dropIndex(['user_id', 'media_type']);
        });

        Schema::table('watch_histories', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'completed', 'last_watched_at']);
            $table->dropIndex(['user_id', 'media_type', 'media_id']);
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropIndex(['read_at']);
        });
    }
};
