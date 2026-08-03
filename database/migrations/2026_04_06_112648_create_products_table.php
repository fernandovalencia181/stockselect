<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Precios
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable(); // Para mostrar precio tachado

            // Stock y variantes simples
            $table->integer('stock')->default(0);
            $table->string('size')->nullable();

            // UX / UI Flags
            $table->boolean('is_active')->default(true);
            $table->string('image_path')->nullable();

            $table->timestamps();
        });
    }
};

