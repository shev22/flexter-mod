<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('media_type', 16);
            $table->unsignedBigInteger('media_id');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('body')->nullable();
            $table->date('watched_on')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'media_type', 'media_id']);
            $table->index(['user_id', 'watched_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_reviews');
    }
};
