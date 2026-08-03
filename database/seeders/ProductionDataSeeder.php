<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SiteSetting;
use App\Models\Coupon;
use Illuminate\Support\Str;

class ProductionDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categorías
        $categories = [
            'Zapatillas',
            'Sudaderas',
            'Pantalones',
            'Camisetas',
            'Chaquetas y Abrigos',
            'Chándals',
            'Bermudas y Pantalones Cortos',
            'Accesorios',
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat)],
                ['name' => $cat]
            );
        }

        // 2. Ajustes del Sitio (Site Settings)
        $settings = [
            ['key' => 'home_title', 'value' => 'Ropa y Zapatillas Outlet hasta -70% | Stock Select', 'label' => 'Título SEO Portada', 'group' => 'marketing'],
            ['key' => 'home_description', 'value' => 'Tienda online de zapatillas y ropa deportiva de marca con descuentos de hasta el 70%. Nike, Adidas, New Balance. Envío rápido en España.', 'label' => 'Descripción SEO Portada', 'group' => 'marketing'],
            ['key' => 'stat_1_value', 'value' => '-70%', 'label' => 'Estadística 1: Valor', 'group' => 'marketing'],
            ['key' => 'stat_1_label', 'value' => 'Descuentos', 'label' => 'Estadística 1: Etiqueta', 'group' => 'marketing'],
            ['key' => 'stat_2_value', 'value' => '5€', 'label' => 'Estadística 2: Valor', 'group' => 'marketing'],
            ['key' => 'stat_2_label', 'value' => 'Envío', 'label' => 'Estadística 2: Etiqueta', 'group' => 'marketing'],
            ['key' => 'legal_shop_name', 'value' => 'STOCK SELECT', 'label' => 'Nombre de la Tienda', 'group' => 'legal'],
            ['key' => 'legal_email', 'value' => 'soporte@stockselect.com', 'label' => 'Email de Soporte', 'group' => 'legal'],
            ['key' => 'legal_owner', 'value' => 'Nombre del Autónomo o Empresa S.L.', 'label' => 'Nombre Titular / Razón Social', 'group' => 'legal'],
            ['key' => 'legal_nif', 'value' => 'B12345678', 'label' => 'NIF / CIF', 'group' => 'legal'],
            ['key' => 'legal_address', 'value' => 'Madrid, España', 'label' => 'Dirección Fiscal (Resumida)', 'group' => 'legal'],
            ['key' => 'legal_city', 'value' => 'Madrid', 'label' => 'Ciudad Jurisdicción', 'group' => 'legal'],
            ['key' => 'legal_full_address', 'value' => 'Calle Falsa 123, 28001 Madrid, España', 'label' => 'Dirección Postal Completa', 'group' => 'legal'],
            ['key' => 'whatsapp_number', 'value' => '34643717157', 'label' => 'Número de WhatsApp', 'group' => 'legal'],
            ['key' => 'whatsapp_default_message', 'value' => '¡Hola! Vengo de la tienda y tengo una duda sobre este producto.', 'label' => 'Mensaje Predeterminado WhatsApp', 'group' => 'legal'],
            ['key' => 'promo_bar_active', 'value' => '0', 'label' => 'Barra de Anuncios Activa', 'group' => 'marketing'],
            ['key' => 'promo_bar_text', 'value' => 'Envíos gratis en pedidos superiores a 50€', 'label' => 'Texto Barra de Anuncios', 'group' => 'marketing'],
            ['key' => 'home_hero_badge', 'value' => 'Actualizado esta semana', 'label' => 'Hero Badge (Texto Pildora)', 'group' => 'marketing'],
            ['key' => 'home_hero_title', 'value' => 'Outlet Premium.', 'label' => 'Título Hero (H1)', 'group' => 'marketing'],
            ['key' => 'home_hero_subtitle', 'value' => 'Productos 100% Originales · Stock limitado', 'label' => 'Subtítulo Hero', 'group' => 'marketing'],
            ['key' => 'social_instagram', 'value' => '', 'label' => 'URL Instagram', 'group' => 'marketing'],
            ['key' => 'social_tiktok', 'value' => '', 'label' => 'URL TikTok', 'group' => 'marketing'],
            ['key' => 'shipping_cost', 'value' => '5', 'label' => 'Coste de Envío Estándar (€)', 'group' => 'tienda'],
            ['key' => 'shipping_free_threshold', 'value' => '50.00', 'label' => 'Umbral Envío Gratis (€)', 'group' => 'tienda'],
            ['key' => 'analytics_id', 'value' => '', 'label' => 'ID Google Analytics / Pixel', 'group' => 'sistema'],
            ['key' => 'maintenance_mode', 'value' => '0', 'label' => 'Modo Mantenimiento Activo', 'group' => 'sistema'],
            ['key' => 'home_featured_title', 'value' => 'Nuestra Selección', 'label' => 'Título Sección Destacada', 'group' => 'general'],
            ['key' => 'home_featured_subtitle', 'value' => 'Esenciales', 'label' => 'Subtítulo Sección Destacada', 'group' => 'general'],
            ['key' => 'product_badge_text', 'value' => 'DESTACADO', 'label' => 'Etiqueta de Producto Destacado', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // 3. Cupones
        Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percent',
                'value' => 10,
                'is_active' => true,
                'requires_subscription' => false,
                'usage_limit' => 500,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'WELCOME5'],
            [
                'type' => 'fixed',
                'value' => 5,
                'is_active' => true,
                'requires_subscription' => true,
                'usage_limit' => 500,
            ]
        );
    }
}
