<?php

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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'session_id')) {
                $table->string('session_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('orders', 'stripe_payment_id')) {
                $table->string('stripe_payment_id')->nullable()->after('session_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['session_id', 'stripe_payment_id']);
        });
    }
};
