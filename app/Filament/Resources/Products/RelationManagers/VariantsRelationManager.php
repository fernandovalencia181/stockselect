<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $title = 'Tallas / Variantes';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('size')
                ->label('Talla')
                ->required()
                ->maxLength(10)
                ->placeholder('Ej: S, M, L, 42'),

            TextInput::make('color')
                ->label('Color')
                ->nullable()
                ->maxLength(40)
                ->placeholder('Opcional: Negro, Blanco...'),

            TextInput::make('stock')
                ->label('Stock')
                ->numeric()
                ->required()
                ->default(0)
                ->minValue(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('size')->label('Talla')->sortable()->grow(),
                TextColumn::make('color')->label('Color')->placeholder('—')->grow(),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
            ])
            ->headerActions([CreateAction::make()->label('Añadir variante')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
