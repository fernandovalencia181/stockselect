<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SiteSetting;

$settings = [
    [
        'key' => 'legal_shop_name',
        'label' => 'Nombre de la Tienda',
        'value' => 'STOCK SELECT',
    ],
    [
        'key' => 'legal_email',
        'label' => 'Email de Soporte',
        'value' => 'soporte@stockselect.com',
    ],
    [
        'key' => 'legal_owner',
        'label' => 'Nombre Titular / Razón Social',
        'value' => 'Nombre del Autónomo o Empresa S.L.',
    ],
    [
        'key' => 'legal_nif',
        'label' => 'NIF / CIF',
        'value' => 'B12345678',
    ],
    [
        'key' => 'legal_address',
        'label' => 'Dirección Fiscal (Resumida)',
        'value' => 'Madrid, España',
    ],
    [
        'key' => 'legal_city',
        'label' => 'Ciudad Jurisdicción',
        'value' => 'Madrid',
    ],
    [
        'key' => 'legal_full_address',
        'label' => 'Dirección Postal Completa',
        'value' => 'Calle Falsa 123, 28001 Madrid, España',
    ],
    [
        'key' => 'whatsapp_number',
        'label' => 'Número de WhatsApp',
        'value' => '34600000000',
    ],
    [
        'key' => 'whatsapp_default_message',
        'label' => 'Mensaje Predeterminado WhatsApp',
        'value' => '¡Hola! Vengo de la tienda y tengo una duda sobre este producto.',
    ],
];

foreach ($settings as $s) {
    SiteSetting::updateOrCreate(['key' => $s['key']], [
        'label' => $s['label'],
        'value' => SiteSetting::getValue($s['key'], $s['value']), // Mantener si ya existe
    ]);
}

echo "Ajustes legales y de WhatsApp inicializados correctamente.\n";
