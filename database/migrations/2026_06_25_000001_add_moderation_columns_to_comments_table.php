<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('is_flagged')->default(false)->after('is_spoiler');
            $table->boolean('is_blocked')->default(false)->after('is_flagged');
            $table->timestamp('flagged_at')->nullable()->after('is_blocked');
            $table->timestamp('blocked_at')->nullable()->after('flagged_at');
            $table->text('admin_notes')->nullable()->after('blocked_at');

            $table->index(['is_flagged', 'created_at']);
            $table->index(['is_blocked', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['is_flagged', 'created_at']);
            $table->dropIndex(['is_blocked', 'created_at']);
            $table->dropColumn(['is_flagged', 'is_blocked', 'flagged_at', 'blocked_at', 'admin_notes']);
        });
    }
};
