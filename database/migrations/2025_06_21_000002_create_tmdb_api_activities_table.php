<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tmdb_api_activities', function (Blueprint $table) {
            $table->id();
            $table->string('operation');
            $table->string('media_type')->nullable();
            $table->string('category')->nullable();
            $table->unsignedSmallInteger('request_count')->default(1);
            $table->unsignedInteger('items_fetched')->default(0);
            $table->string('source')->default('web');
            $table->string('status')->default('success');
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index(['media_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tmdb_api_activities');
    }
};
