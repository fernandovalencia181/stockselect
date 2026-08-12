<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;
    protected static ?string $navigationLabel = 'Ajustes Generales';
    protected static ?string $modelLabel = 'Ajuste';
    protected static ?string $pluralModelLabel = 'Gestión de la Tienda';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Información del Campo')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Nombre descriptivo')
                                    ->disabled()
                                    ->required(),
                                TextInput::make('key')
                                    ->label('Identificador técnico (No editar)')
                                    ->disabled()
                                    ->required(),
                            ]),
                        Tab::make('Valor y Configuración')
                            ->icon('heroicon-m-pencil-square')
                            ->schema(function ($record) {
                                $key = $record?->key;

                                // 1. Toggles (Booleano)
                                if (in_array($key, ['promo_bar_active', 'maintenance_mode', 'footer_show_payment_methods'])) {
                                    return [
                                        Toggle::make('value')
                                            ->label('Mostrar / Activar')
                                            ->afterStateHydrated(fn (Toggle $component, $state) => $component->state((bool) $state))
                                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),
                                    ];
                                }

                                // 1b. Modo de Checkout
                                if ($key === 'checkout_mode') {
                                    return [
                                        Select::make('value')
                                            ->label('Modo de Checkout')
                                            ->options([
                                                'simple'    => '🤝  Entrega en mano (1 paso — sin dirección, pago en efectivo)',
                                                'multistep' => '📦  Envíos a domicilio (3 pasos — con dirección de envío)',
                                            ])
                                            ->required()
                                            ->helperText('Entrega en mano: formulario rápido, el cliente coordina el punto de recogida por WhatsApp. Envíos: guía al cliente por Datos → Dirección → Pago y genera la dirección de envío.'),
                                    ];
                                }

                                // 2. Precios y Umbrales (Numérico)
                                if (in_array($key, ['shipping_cost', 'shipping_free_threshold'])) {
                                    return [
                                        TextInput::make('value')
                                            ->label('Valor Numérico')
                                            ->numeric()
                                            ->prefix('€'),
                                    ];
                                }

                                // 3. Textos largos
                                if (in_array($key, ['home_hero_subtitle', 'promo_bar_text', 'site_description'])) {
                                    return [
                                        Textarea::make('value')
                                            ->label('Contenido de Texto')
                                            ->rows(3),
                                    ];
                                }

                                // 4. Archivos / Imágenes
                                if (in_array($key, ['site_logo', 'site_favicon', 'seo_og_image', 'seo_og_image_default'])) {
                                    return [
                                        \Filament\Forms\Components\FileUpload::make('value')
                                            ->label('Seleccionar Imagen')
                                            ->image()
                                            ->disk('public')
                                            ->saveUploadedFileUsing(function (\Illuminate\Http\UploadedFile $file) {
                                                return \App\Utils\ImageHelper::optimizeToWebp($file, 'settings');
                                            }),
                                    ];
                                }

                                // 5. Por defecto: Texto corto
                                return [
                                    TextInput::make('value')
                                        ->label('Valor')
                                        ->required(),
                                ];
                            }),
                    ]),
            ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Elemento')
                    ->sortable(),
                TextColumn::make('value')
                    ->label('Valor Actual')
                    ->limit(50),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }

    // Deshabilitar creación para evitar desorden
    public static function canCreate(): bool
    {
        return false;
    }
}
