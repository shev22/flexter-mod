<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('theme')->default('dark');
            $table->string('accent')->default('aurora');
            $table->boolean('autoplay_trailers')->default(true);
            $table->boolean('reduce_motion')->default(false);
            $table->boolean('subtitles')->default(true);
            $table->string('maturity')->default('all');
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
