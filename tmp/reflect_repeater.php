<?php
require 'vendor/autoload.php';

$repeater = \Filament\Forms\Components\Repeater::make('test');
$methods = get_class_methods($repeater);
sort($methods);
echo implode("\n", $methods);
