#!/usr/bin/env php
<?php

/**
 * Re-aplica el parche al vendor Number.php de Laravel para permitir que
 * la app funcione sin ext-intl disponible en el contexto del servidor web (Windows/WAMP).
 * Este script se ejecuta automáticamente en post-install-cmd y post-update-cmd.
 */

$file = __DIR__ . '/../vendor/laravel/framework/src/Illuminate/Support/Number.php';

if (! file_exists($file)) {
    echo "Number.php no encontrado, saltando parche." . PHP_EOL;
    exit(0);
}

$contents = file_get_contents($file);

// Verificar si ya está parcheado
if (strpos($contents, 'Fallback nativo cuando ext-intl') !== false) {
    echo "Number.php ya está parcheado, nada que hacer." . PHP_EOL;
    exit(0);
}

// Código original a reemplazar
$original = <<<'PHP'
    public static function format(int|float $number, ?int $precision = null, ?int $maxPrecision = null, ?string $locale = null)
    {
        static::ensureIntlExtensionIsInstalled();

        $formatter = new NumberFormatter($locale ?? static::$locale, NumberFormatter::DECIMAL);
PHP;

// Código parcheado
$patched = <<<'PHP'
    public static function format(int|float $number, ?int $precision = null, ?int $maxPrecision = null, ?string $locale = null)
    {
        // Fallback nativo cuando ext-intl no está disponible en el contexto web
        if (! extension_loaded('intl')) {
            $decimals = $maxPrecision ?? $precision ?? 0;
            return number_format($number, $decimals);
        }

        $formatter = new NumberFormatter($locale ?? static::$locale, NumberFormatter::DECIMAL);
PHP;

if (strpos($contents, $original) === false) {
    echo "AVISO: El parche no puede aplicarse (el código original no coincide). Puede que ya lo haya cambiado una actualización." . PHP_EOL;
    exit(1);
}

$patched_contents = str_replace($original, $patched, $contents);
file_put_contents($file, $patched_contents);

echo "✅ Parche aplicado correctamente a Number.php." . PHP_EOL;
