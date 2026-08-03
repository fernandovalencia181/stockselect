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
        Schema::table('products', function (Blueprint $table) {
            $table->string('color_name')->nullable()->after('brand');
            $table->string('color_hex')->nullable()->after('color_name');
            $table->string('model_group')->nullable()->index()->after('color_hex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['model_group']);
            $table->dropColumn(['color_name', 'color_hex', 'model_group']);
        });
    }
};
