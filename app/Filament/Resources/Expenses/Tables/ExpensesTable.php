<?php

namespace App\Filament\Resources\Expenses\Tables;

use App\Models\Expense;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('expense_date', 'desc')
            ->columns([
                TextColumn::make('expense_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Expense::categoryLabels()[$state] ?? $state)
                    ->color(fn ($state) => Expense::categoryColors()[$state] ?? 'gray'),

                TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('amount')
                    ->label('Importe')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2, ',', '.') . ' €')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(Expense::categoryLabels()),
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
