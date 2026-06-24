<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flexter_lists', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('flexter_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flexter_list_id')->constrained()->cascadeOnDelete();
            $table->string('media_type', 16);
            $table->unsignedBigInteger('media_id');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['flexter_list_id', 'media_type', 'media_id'], 'flexter_list_items_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flexter_list_items');
        Schema::dropIfExists('flexter_lists');
    }
};
