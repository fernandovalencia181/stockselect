<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'category',
        'description',
        'amount',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Etiquetas legibles para cada categoría.
     */
    public static function categoryLabels(): array
    {
        return [
            'packaging' => 'Packaging',
            'shipping'  => 'Envío / Mensajería',
            'other'     => 'Otros',
        ];
    }

    /**
     * Colores de badge por categoría (para Filament).
     */
    public static function categoryColors(): array
    {
        return [
            'packaging' => 'warning',
            'shipping'  => 'info',
            'other'     => 'gray',
        ];
    }

    /**
     * Scope que filtra por período igual que el dashboard.
     * @param string|null $filter  '7d' | '30d' | '12m' | 'all'
     */
    public function scopeInPeriod(Builder $query, ?string $filter): Builder
    {
        return match ($filter) {
            '7d'  => $query->where('expense_date', '>=', now()->subDays(7)->toDateString()),
            '30d' => $query->where('expense_date', '>=', now()->subDays(30)->toDateString()),
            '12m' => $query->where('expense_date', '>=', now()->subMonths(12)->toDateString()),
            default => $query, // 'all'
        };
    }
}
