<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('images');
        });

        // Inicializar sort_order con el orden actual (más nuevos = sort_order más alto)
        // Así los clientes siguen viendo los nuevos primero igual que antes
        $products = DB::table('products')->orderBy('created_at', 'desc')->pluck('id');
        foreach ($products as $index => $id) {
            DB::table('products')->where('id', $id)->update(['sort_order' => $index]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
