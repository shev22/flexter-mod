<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genre_tv', function (Blueprint $table) {
            $table->unsignedBigInteger('genre_id');
            $table->unsignedBigInteger('tv_id');

            $table->foreign('genre_id')
                ->references('id')
                ->on('genres')->onDelete('cascade');

            $table->foreign('tv_id')
                ->references('id')
                ->on('tv')->onDelete('cascade');

            $table->unique(['genre_id', 'tv_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genre_tv');
    }
};
