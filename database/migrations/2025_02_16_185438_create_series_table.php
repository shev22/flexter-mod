<?php

use App\Enums\Categories;
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
        Schema::create('series', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('backdrop_path')->nullable();
            $table->string('title')->nullable();
            $table->string('genre_ids')->nullable();
            $table->string('logo')->nullable();
            $table->enum('category', Categories::getValues());
            $table->string('original_language')->nullable();
            $table->longText ('overview')->nullable();
            $table->string('popularity')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('release_date')->nullable();
            $table->string('is_trending')->default(false);
            $table->string('trailer')->nullable();
            $table->string('runtime')->nullable();
            $table->string('vote_average')->nullable();
            $table->string('vote_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
