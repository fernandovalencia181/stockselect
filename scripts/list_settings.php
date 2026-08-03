<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SiteSetting;

foreach(SiteSetting::all() as $setting) {
    echo "{$setting->key} | {$setting->label} | {$setting->value}\n";
}
