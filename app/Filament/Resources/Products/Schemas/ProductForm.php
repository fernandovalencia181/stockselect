<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Storage;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // ──────────────────────────────────────────────
                // FILA 1: Info principal + Precios (lado a lado en desktop)
                // ──────────────────────────────────────────────
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make('Información del Producto')
                            ->columns(1)
                            ->schema([
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->label('Categoría')
                                    ->required(),
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->default('Adidas')
                                    ->required(),
                                TextInput::make('model_group')
                                    ->label('Modelo/Grupo')
                                    ->placeholder('Ej: adidas-forum-low')
                                    ->helperText('Vincula colores.')
                                    ->required(),
                                Select::make('gender')
                                    ->label('Género')
                                    ->options(\App\Models\Product::getGenderOptions())
                                    ->default('unisex')
                                    ->required(),
                                Select::make('fit_type')
                                    ->label('📏 Recomendación de Tallaje / Ajuste')
                                    ->options(\App\Models\Product::getFitOptions())
                                    ->placeholder('Sin recomendación (opcional)')
                                    ->helperText('Avisa al cliente si la prenda talla grande, pequeña o normal.'),
                                TextInput::make('fit_advice')
                                    ->label('Consejo personalizado de talla (Opcional)')
                                    ->placeholder('Ej: El modelo mide 1.82m y lleva una talla L.')
                                    ->columnSpanFull(),
                                Textarea::make('description')
                                    ->label('Descripción')
                                    ->columnSpanFull()
                                    ->rows(3),
                            ]),

                        Section::make('Precios y Estado')
                            ->columns(1)
                            ->schema([
                                TextInput::make('price')
                                    ->label('Precio Venta')
                                    ->required()
                                    ->numeric()
                                    ->prefix('€'),
                                TextInput::make('original_price')
                                    ->label('Precio Original (Tachado)')
                                    ->numeric()
                                    ->prefix('€'),
                                TextInput::make('cost_price')
                                    ->label('Precio Coste')
                                    ->numeric()
                                    ->prefix('€'),
                                Placeholder::make('net_profit')
                                    ->label('Margen Neto (Stripe)')
                                    ->content(function ($get) {
                                        $price = (float) $get('price');
                                        $cost = (float) $get('cost_price');
                                        if (!$price || !$cost)
                                            return '---';
                                        $fees = ($price * 0.015) + 0.25;
                                        $profit = $price - $cost - $fees;
                                        $color = $profit >= 0 ? 'text-green-600' : 'text-red-600';
                                        return new HtmlString("<span class='font-bold {$color}'>" . number_format($profit, 2) . "€</span>");
                                    }),
                                TextInput::make('color_name')
                                    ->label('Nombre Color'),
                                ColorPicker::make('color_hex')
                                    ->label('Color Círculo')
                                    ->suffixAction(
                                        Action::make('eyeDropper')
                                            ->icon('heroicon-m-eye-dropper')
                                            ->tooltip('Capturar color de imagen (Cuentagotas)')
                                            ->color('gray')
                                            ->alpineClickHandler("
                                                if (! ('EyeDropper' in window)) {
                                                    alert('El cuentagotas no está disponible. Asegúrate de: 1. Usar Chrome/Edge. 2. Que la conexión sea segura (HTTPS o localhost).');
                                                    return;
                                                }
                                                
                                                const dropper = new EyeDropper();
                                                dropper.open()
                                                    .then(result => {
                                                        const color = result.sRGBHex;
                                                        \$wire.set('data.color_hex', color);
                                                    })
                                                    .catch(e => {
                                                        console.log('Selección cancelada');
                                                    });
                                            ")
                                    ),
                                Toggle::make('is_featured')
                                    ->label('Destacado'),
                                Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true),
                                Toggle::make('has_replacement_box')
                                    ->label('📦 Caja de sustitución (Genérica)')
                                    ->helperText('Actívalo si no incluye la caja original de la marca.')
                                    ->live(),
                                TextInput::make('packaging_notice')
                                    ->label('Aviso personalizado de caja (Opcional)')
                                    ->placeholder('Ej: Producto 100% original y nuevo. Se envía en caja protectora neutra.')
                                    ->visible(fn ($get) => (bool) $get('has_replacement_box')),
                            ]),
                    ]),

                // ──────────────────────────────────────────────
                // FILA 2: Galería (ANTES de variantes para acceso rápido)
                // ──────────────────────────────────────────────
                Section::make('Galería')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        FileUpload::make('images')
                            ->disk('public')
                            ->directory('products')
                            ->multiple()
                            ->image()
                            ->panelLayout('grid')
                            ->visibility('public')
                            ->imagePreviewHeight('250')
                            ->loadingIndicatorPosition('overlay')
                            ->removeUploadedFileButtonPosition('top-right')
                            ->saveUploadedFileUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file) {
                                return \App\Utils\ImageHelper::optimizeToWebp($file, 'products');
                            }),
                    ]),

                // ──────────────────────────────────────────────
                // FILA 3: Variantes (tallas/stock)
                // ──────────────────────────────────────────────
                Section::make('Variantes (Existencias)')
                    ->collapsible()
                    ->headerActions([
                        Action::make('generateSizes')
                            ->label('Generar Tallas')
                            ->icon('heroicon-m-sparkles')
                            ->color('info')
                            ->form([
                                Select::make('tipo_tallaje')
                                    ->label('Tipo de Tallaje')
                                    ->options([
                                        'calzado' => 'Calzado Adidas',
                                        'ropa' => 'Ropa Letras',
                                        'pantalones' => 'Pantalones',
                                    ])
                                    ->required(),
                            ])
                            ->action(function (array $data, Set $set) {
                                $sizes = match ($data['tipo_tallaje']) {
                                    'calzado' => ['36', '36 2/3', '37 1/3', '38', '38 2/3', '39 1/3', '40', '40 2/3', '41 1/3', '42', '42 2/3', '43 1/3', '44', '44 2/3', '45 1/3', '46', '46 2/3', '47 1/3', '48'],
                                    'ropa' => ['XXS', 'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'],
                                    'pantalones' => ['32', '34', '36', '38', '40', '42', '44', '46'],
                                    default => [],
                                };
                                $variants = array_map(fn($size) => ['size' => $size, 'stock' => 0], $sizes);
                                $set('variants', $variants);
                            })
                    ])
                    ->schema([
                        Repeater::make('variants')
                            ->relationship()
                            ->label('Variantes')
                            ->schema([
                                TextInput::make('size')->required()->label('Talla'),
                                TextInput::make('stock')->required()->numeric()->label('Stock'),
                            ])
                            ->columns(1)
                            ->grid(['default' => 1, 'md' => 2])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
