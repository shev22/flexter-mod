<?php

use App\Enums\MediaType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movie_id');
            $table->string('backdrop_path')->nullable();
            $table->string('title')->nullable();
            $table->string('logo')->nullable();
            $table->string('original_language')->nullable();
            $table->longText ('overview')->nullable();
            $table->string('popularity')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('release_date')->nullable();
            $table->string('vote_average')->nullable();
            $table->string('vote_count')->nullable();
            $table->string('year')->nullable();
            $table->enum('media_type', MediaType::getValues());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
