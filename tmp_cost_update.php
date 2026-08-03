<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = App\Models\OrderItem::with('product')->get();
$count = 0;
foreach($items as $item) {
    if($item->product && $item->product->cost_price > 0) {
        $item->cost_price_at_time = $item->product->cost_price;
        $item->save();
        $count++;
    }
}
echo "Actualizados $count articulos de pedidos.\n";
