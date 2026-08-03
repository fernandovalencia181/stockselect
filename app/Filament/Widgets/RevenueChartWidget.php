<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Evolución de Beneficios';

    protected ?string $maxHeight = '300px';

    protected static ?int $sort = -2;

    public ?string $filter = '30d';

    protected function getFilters(): ?array
    {
        return [
            '7d'  => 'Últimos 7 días',
            '30d' => 'Últimos 30 días',
            '12m' => 'Últimos 12 meses',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter;

        if ($filter === '12m') {
            return $this->getMonthlyData();
        }

        $days = $filter === '7d' ? 7 : 30;
        return $this->getDailyData($days);
    }

    private function getDailyData(int $days): array
    {
        $labels       = [];
        $revenueData  = [];
        $profitData   = [];
        $expenseData  = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date    = now()->subDays($i);
            $dateStr = $date->toDateString();

            $labels[] = $date->format('d M');

            $revenue = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
                ->whereDate('created_at', $dateStr)
                ->sum('total_amount');

            $cost = OrderItem::whereHas('order', fn ($q) => $q->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->whereDate('created_at', $dateStr))
                ->selectRaw('SUM(cost_price_at_time * quantity) as total_cost')
                ->value('total_cost') ?? 0;

            $expenses = Expense::whereDate('expense_date', $dateStr)->sum('amount');

            $revenueData[] = round((float) $revenue, 2);
            $profitData[]  = round((float) ($revenue - $cost - $expenses), 2);
            $expenseData[] = round((float) $expenses, 2);
        }

        return $this->buildDatasets($labels, $revenueData, $profitData, $expenseData);
    }

    private function getMonthlyData(): array
    {
        $labels       = [];
        $revenueData  = [];
        $profitData   = [];
        $expenseData  = [];

        for ($i = 11; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $year  = $date->year;
            $month = $date->month;

            $labels[] = $date->translatedFormat('M Y');

            $revenue = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('total_amount');

            $cost = OrderItem::whereHas('order', fn ($q) => $q->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month))
                ->selectRaw('SUM(cost_price_at_time * quantity) as total_cost')
                ->value('total_cost') ?? 0;

            $expenses = Expense::whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month)
                ->sum('amount');

            $revenueData[] = round((float) $revenue, 2);
            $profitData[]  = round((float) ($revenue - $cost - $expenses), 2);
            $expenseData[] = round((float) $expenses, 2);
        }

        return $this->buildDatasets($labels, $revenueData, $profitData, $expenseData);
    }

    private function buildDatasets(array $labels, array $revenue, array $profit, array $expenses): array
    {
        return [
            'datasets' => [
                [
                    'label'           => 'Ingresos',
                    'data'            => $revenue,
                    'borderColor'     => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Beneficio Neto',
                    'data'            => $profit,
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Gastos Operacionales',
                    'data'            => $expenses,
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.08)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
