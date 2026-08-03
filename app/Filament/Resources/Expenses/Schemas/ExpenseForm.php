<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Models\Expense;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('expense_date')
                ->label('Fecha del gasto')
                ->required()
                ->default(now())
                ->native(false)
                ->displayFormat('d/m/Y'),

            Select::make('category')
                ->label('Categoría')
                ->required()
                ->options(Expense::categoryLabels())
                ->default('other'),

            TextInput::make('description')
                ->label('Descripción')
                ->required()
                ->maxLength(255)
                ->placeholder('Ej: 50 cajas de cartón, DHL envío 5 pedidos…')
                ->columnSpanFull(),

            TextInput::make('amount')
                ->label('Importe (€)')
                ->required()
                ->numeric()
                ->step(0.01)
                ->minValue(0.01)
                ->prefix('€'),

            Textarea::make('notes')
                ->label('Notas adicionales')
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),
        ]);
    }
}
