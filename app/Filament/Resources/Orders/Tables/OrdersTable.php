<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('id')
                    ->label('# Pedido')
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('customer_phone')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'warning',
                        'paid'       => 'info',
                        'processing' => 'indigo',
                        'shipped'    => 'teal',
                        'delivered'  => 'success',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'    => 'Pendiente',
                        'paid'       => 'Pagado',
                        'processing' => 'En preparación',
                        'shipped'    => 'Enviado',
                        'delivered'  => 'Entregado',
                        default      => $state,
                    })
                    ->searchable(),
                TextColumn::make('shipping_method')
                    ->label('Envío')
                    ->formatStateUsing(fn ($state) => $state === 'local_pickup' ? '🏠 Recogida' : '📦 Envío')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '€' . number_format((float) $state, 2, ',', '.'))
                    ->sortable(),
                TextColumn::make('shipping_cost')
                    ->label('Coste envío')
                    ->formatStateUsing(fn ($state) => '€' . number_format((float) $state, 2, ',', '.'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
