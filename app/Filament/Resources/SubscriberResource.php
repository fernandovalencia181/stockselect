<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriberResource\Pages;
use App\Models\Subscriber;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Collection;
use BackedEnum;
use UnitEnum;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope-open';
    protected static UnitEnum|string|null $navigationGroup = 'Marketing';
    protected static ?string $modelLabel = 'Suscriptor';
    protected static ?string $pluralModelLabel = 'Suscriptores';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name'),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Activo',
                        'unsubscribed' => 'Desuscrito',
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\TextInput::make('source')
                    ->disabled()
                    ->default('manual'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'unsubscribed' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Activo',
                        'unsubscribed' => 'Desuscrito',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('export_csv')
                    ->label('Exportar a CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        $filename = 'subscribers_' . now()->format('Y-m-d_H-i-s') . '.csv';
                        $headers = [
                            "Content-type"        => "text/csv",
                            "Content-Disposition" => "attachment; filename=$filename",
                            "Pragma"              => "no-cache",
                            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                            "Expires"             => "0"
                        ];
                        $callback = function() use($records) {
                            $file = fopen('php://output', 'w');
                            fputcsv($file, ['Email', 'Name', 'Status', 'Date']);
                            foreach ($records as $record) {
                                fputcsv($file, [$record->email, $record->name, $record->status, $record->created_at]);
                            }
                            fclose($file);
                        };
                        return response()->stream($callback, 200, $headers);
                    })
                    ->deselectRecordsAfterCompletion(),
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscribers::route('/'),
        ];
    }
}
