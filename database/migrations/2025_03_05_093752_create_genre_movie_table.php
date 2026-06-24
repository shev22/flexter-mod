<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genre_movie', function (Blueprint $table) {
            $table->unsignedBigInteger('genre_id');
            $table->unsignedBigInteger('movie_id');

            $table->foreign('genre_id')
                ->references('id')
                ->on('genres')->onDelete('cascade');

            $table->foreign('movie_id')
                ->references('id')
                ->on('movies')->onDelete('cascade');

            $table->unique(['genre_id', 'movie_id']);
        });
    }

    public function down(): void
    {
        Schema::table('genre_movie', function (Blueprint $table) {
            Schema::dropIfExists('genre_movie');
        });
    }
};
