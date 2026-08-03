<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\ManageCoupons;
use App\Models\Coupon;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Filament\Forms\Components;
use Filament\Support\Icons\Heroicon;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static \UnitEnum|string|null $navigationGroup = 'Marketing';
    protected static ?string $modelLabel = 'Cupón';
    protected static ?string $pluralModelLabel = 'Cupones';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->unique(ignoreRecord: true),
                Components\ToggleButtons::make('type')
                    ->label('Tipo de Descuento')
                    ->options([
                        'percent' => 'Porcentaje (%)',
                        'fixed' => 'Cantidad Fija (€)',
                    ])
                    ->icons([
                        'percent' => 'heroicon-o-receipt-percent',
                        'fixed' => 'heroicon-o-banknotes',
                    ])
                    ->required()
                    ->inline()
                    ->default('percent')
                    ->live(),
                Components\TextInput::make('value')
                    ->label('Valor')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix(fn ($get) => $get('type') === 'fixed' ? '€' : null)
                    ->suffix(fn ($get) => $get('type') === 'percent' ? '%' : null)
                    ->live(),
                Components\TextInput::make('usage_limit')
                    ->label('Límite de usos')
                    ->numeric()
                    ->minValue(1),
                Components\DateTimePicker::make('expires_at')
                    ->label('Expira el'),
                Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
                Components\Toggle::make('requires_subscription')
                    ->label('Solo Suscriptores')
                    ->helperText('Si se activa, el cupón solo funcionará para emails registrados en la lista de suscriptores.')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percent' => 'info',
                        'fixed' => 'success',
                    }),
                Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->getStateUsing(function ($record) {
                        if ($record->type === 'fixed') {
                            return number_format($record->value, 2) . ' €';
                        }
                        return $record->value . ' %';
                    }),
                Columns\TextColumn::make('used_count')
                    ->label('Usos')
                    ->suffix(fn ($record) => $record->usage_limit ? " / {$record->usage_limit}" : ''),
                Columns\ToggleColumn::make('is_active')
                    ->label('Activo'),
                Columns\IconColumn::make('requires_subscription')
                    ->label('Susc.')
                    ->boolean()
                    ->trueIcon('heroicon-o-users')
                    ->falseIcon('heroicon-o-minus')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Nunca'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCoupons::route('/'),
        ];
    }
}
