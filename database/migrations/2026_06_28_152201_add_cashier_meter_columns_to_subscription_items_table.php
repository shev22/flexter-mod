<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Newer Cashier versions publish meter columns separately.
 * Apply them here only when missing so publish + custom migrations can coexist.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscription_items')) {
            return;
        }

        Schema::table('subscription_items', function (Blueprint $table) {
            if (! Schema::hasColumn('subscription_items', 'meter_id')) {
                $table->string('meter_id')->nullable()->after('stripe_price');
            }

            if (! Schema::hasColumn('subscription_items', 'meter_event_name')) {
                $table->string('meter_event_name')->nullable()->after('quantity');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('subscription_items')) {
            return;
        }

        Schema::table('subscription_items', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('subscription_items', 'meter_id')) {
                $columns[] = 'meter_id';
            }

            if (Schema::hasColumn('subscription_items', 'meter_event_name')) {
                $columns[] = 'meter_event_name';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
