<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insertar solo si no existe
        if (!DB::table('site_settings')->where('key', 'checkout_mode')->exists()) {
            DB::table('site_settings')->insert([
                'key'        => 'checkout_mode',
                'value'      => 'simple',
                'label'      => 'Modo de Checkout',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('site_settings')->where('key', 'checkout_mode')->delete();
    }
};
