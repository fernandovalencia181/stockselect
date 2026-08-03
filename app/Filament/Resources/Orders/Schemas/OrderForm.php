<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('created_at')
                    ->label('Fecha y Hora de Compra')
                    ->disabled()
                    ->format('d/m/Y H:i')
                    ->displayFormat('d/m/Y H:i'),
                Select::make('status')
                    ->label('Estado del Pedido')
                    ->options([
                        'pending' => 'Recibido',
                        'paid' => 'Pagado',
                        'processing' => 'En preparación',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregado',
                        'returned' => 'Devuelto',
                        'cancelled' => 'Cancelado',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('tracking_number')
                    ->label('Número de Seguimiento (Tracking)')
                    ->helperText('Añade el código de Packlink/InPost antes de guardar el estado "Enviado".')
                    ->maxLength(255),
                TextInput::make('total_amount')
                    ->label('Monto Total')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->disabled(),
                TextInput::make('shipping_cost')
                    ->label('Costo de Envío')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->disabled(),
                TextInput::make('shipping_method')
                    ->label('Método de Envío')
                    ->required()
                    ->disabled(),
                TextInput::make('customer_name')
                    ->label('Nombre del Cliente')
                    ->required()
                    ->disabled(),
                TextInput::make('customer_email')
                    ->label('Email del Cliente')
                    ->email()
                    ->required()
                    ->disabled(),
                TextInput::make('customer_phone')
                    ->label('Teléfono del Cliente')
                    ->tel()
                    ->required()
                    ->disabled(),
                Textarea::make('shipping_address')
                    ->label('Dirección de Envío')
                    ->columnSpanFull()
                    ->disabled(),
                Repeater::make('items')
                    ->relationship()
                    ->label('Productos Comprados')
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->label('Producto')
                            ->disabled(),
                        Select::make('variant_id')
                            ->relationship('variant', 'size')
                            ->label('Talla / Variante')
                            ->disabled(),
                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->disabled(),
                        TextInput::make('price_at_time')
                            ->label('Precio Unitario')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->addable(false)
                    ->deletable(false),
            ]);
    }
}
