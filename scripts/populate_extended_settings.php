<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SiteSetting;

$settings = [
    // LEGAL Y CONTACTO
    ['key' => 'legal_shop_name', 'label' => 'Nombre de la Tienda', 'group' => 'legal', 'value' => 'STOCK SELECT'],
    ['key' => 'legal_email', 'label' => 'Email de Soporte', 'group' => 'legal', 'value' => 'soporte@stockselect.com'],
    ['key' => 'legal_owner', 'label' => 'Nombre Titular / Razón Social', 'group' => 'legal', 'value' => 'Nombre del Autónomo o Empresa S.L.'],
    ['key' => 'legal_nif', 'label' => 'NIF / CIF', 'group' => 'legal', 'value' => 'B12345678'],
    ['key' => 'legal_address', 'label' => 'Dirección Fiscal (Resumida)', 'group' => 'legal', 'value' => 'Madrid, España'],
    ['key' => 'legal_city', 'label' => 'Ciudad Jurisdicción', 'group' => 'legal', 'value' => 'Madrid'],
    ['key' => 'legal_full_address', 'label' => 'Dirección Postal Completa', 'group' => 'legal', 'value' => 'Calle Falsa 123, 28001 Madrid, España'],
    ['key' => 'whatsapp_number', 'label' => 'Número de WhatsApp', 'group' => 'legal', 'value' => '34600000000'],
    ['key' => 'whatsapp_default_message', 'label' => 'Mensaje Predeterminado WhatsApp', 'group' => 'legal', 'value' => '¡Hola! Vengo de la tienda y tengo una duda sobre este producto.'],

    // MARKETING Y PORTADA (HOME)
    ['key' => 'home_title', 'label' => 'Título SEO Portada', 'group' => 'marketing', 'value' => 'Stock Select | Selección Premium'],
    ['key' => 'home_description', 'label' => 'Descripción SEO Portada', 'group' => 'marketing', 'value' => 'Outlet de ropa deportiva y moda urbana con descuentos de hasta el 70%.'],
    ['key' => 'stat_1_value', 'label' => 'Estadística 1: Valor', 'group' => 'marketing', 'value' => '+5k'],
    ['key' => 'stat_1_label', 'label' => 'Estadística 1: Etiqueta', 'group' => 'marketing', 'value' => 'Clientes Felices'],
    ['key' => 'stat_2_value', 'label' => 'Estadística 2: Valor', 'group' => 'marketing', 'value' => '24h'],
    ['key' => 'stat_2_label', 'label' => 'Estadística 2: Etiqueta', 'group' => 'marketing', 'value' => 'Envío Rápido'],
    
    ['key' => 'promo_bar_active', 'label' => 'Barra de Anuncios Activa', 'group' => 'marketing', 'value' => '0'],
    ['key' => 'promo_bar_text', 'label' => 'Texto Barra de Anuncios', 'group' => 'marketing', 'value' => 'Envíos gratis en pedidos superiores a 50€'],
    ['key' => 'home_hero_badge', 'label' => 'Hero Badge (Texto Pildora)', 'group' => 'marketing', 'value' => 'Stock especial · Actualizado esta semana'],
    ['key' => 'home_hero_title', 'label' => 'Título Hero (H1)', 'group' => 'marketing', 'value' => 'Selección Premium.'],
    ['key' => 'home_hero_subtitle', 'label' => 'Subtítulo Hero', 'group' => 'marketing', 'value' => "Ropa urbana y zapatillas deportivas con descuentos irrepetibles.\nStock muy limitado."],
    
    // REDES SOCIALES (Las meteré en Marketing o una nueva pestaña)
    ['key' => 'social_instagram', 'label' => 'URL Instagram', 'group' => 'marketing', 'value' => ''],
    ['key' => 'social_tiktok', 'label' => 'URL TikTok', 'group' => 'marketing', 'value' => ''],
    
    // REGLAS DE TIENDA
    ['key' => 'shipping_cost', 'label' => 'Coste de Envío Estándar (€)', 'group' => 'tienda', 'value' => '4.00'],
    ['key' => 'shipping_free_threshold', 'label' => 'Umbral Envío Gratis (€)', 'group' => 'tienda', 'value' => '50.00'],
    
    // ANALYTICS & SISTEMA
    ['key' => 'analytics_id', 'label' => 'ID Google Analytics / Pixel', 'group' => 'sistema', 'value' => ''],
    ['key' => 'maintenance_mode', 'label' => 'Modo Mantenimiento Activo', 'group' => 'sistema', 'value' => '0'],
];

foreach ($settings as $s) {
    SiteSetting::updateOrCreate(['key' => $s['key']], [
        'label' => $s['label'],
        'group' => $s['group'],
        'value' => SiteSetting::where('key', $s['key'])->first()?->value ?? $s['value'],
    ]);
}

echo "Ajustes extendidos y categorizados inicializados correctamente.\n";
