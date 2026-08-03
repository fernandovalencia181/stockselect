<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Añadir product_variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size');
            $table->string('color')->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        // 2. Quitar size y stock de products
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['size', 'stock']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');

        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(0);
            $table->string('size')->nullable();
        });
    }
};
