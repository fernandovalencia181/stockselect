<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class FinancialStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -3;

    public ?string $filter = '30d';

    public function getFilters(): array
    {
        return [
            '7d'  => 'Últimos 7 días',
            '30d' => 'Últimos 30 días',
            '12m' => 'Últimos 12 meses',
            'all' => 'Todo el tiempo',
        ];
    }

    public function updatedFilter(): void
    {
        $this->cachedStats = null;
    }

    public function getSectionContentComponent(): Component
    {
        $filters = $this->getFilters();
        $current = $this->filter;

        $options = collect($filters)
            ->map(fn ($label, $value) =>
                '<option value="' . e($value) . '"' . ($current === $value ? ' selected' : '') . '>' . e($label) . '</option>'
            )
            ->implode('');

        $filterHtml = Blade::render(<<<BLADE
            <x-filament::input.wrapper inline-prefix wire:target="filter" class="fi-wi-chart-filter">
                <x-filament::input.select inline-prefix wire:model.live="filter">
                    {!! \$options !!}
                </x-filament::input.select>
            </x-filament::input.wrapper>
        BLADE, ['options' => $options]);

        return Section::make()
            ->heading('Resumen Financiero')
            ->afterHeader(new HtmlString($filterHtml))
            ->schema($this->getCachedStats())
            ->columns($this->getColumns())
            ->contained(false)
            ->gridContainer();
    }

    // ── Queries ───────────────────────────────────────────────────────────────

    private function paidOrdersQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered']);
        match ($this->filter) {
            '7d'  => $query->where('created_at', '>=', now()->subDays(7)->startOfDay()),
            '30d' => $query->where('created_at', '>=', now()->subDays(30)->startOfDay()),
            '12m' => $query->where('created_at', '>=', now()->subMonths(12)->startOfDay()),
            default => null,
        };
        return $query;
    }

    private function cogsQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $orderQuery = fn ($q) => match ($this->filter) {
            '7d'  => $q->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->where('created_at', '>=', now()->subDays(7)->startOfDay()),
            '30d' => $q->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->where('created_at', '>=', now()->subDays(30)->startOfDay()),
            '12m' => $q->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->where('created_at', '>=', now()->subMonths(12)->startOfDay()),
            default => $q->whereIn('status', ['paid', 'processing', 'shipped', 'delivered']),
        };
        return OrderItem::whereHas('order', $orderQuery);
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

    protected function getStats(): array
    {
        $paidOrders     = $this->paidOrdersQuery();
        $totalRevenue   = (clone $paidOrders)->sum('total_amount');
        $paidOrderCount = (clone $paidOrders)->count();

        $cogs = $this->cogsQuery()
            ->selectRaw('SUM(cost_price_at_time * quantity) as total_cost')
            ->value('total_cost') ?? 0;

        // Gastos operacionales del período
        $packagingCost = Expense::inPeriod($this->filter)
            ->where('category', 'packaging')
            ->sum('amount');

        $shippingCost = Expense::inPeriod($this->filter)
            ->where('category', 'shipping')
            ->sum('amount');

        $otherCost = Expense::inPeriod($this->filter)
            ->where('category', 'other')
            ->sum('amount');

        $totalOperationalCost = $packagingCost + $shippingCost + $otherCost;

        // Comisiones Stripe
        $gatewayFees = ($totalRevenue * 0.029) + ($paidOrderCount * 0.25);

        // Beneficio neto real (ingresos - COGS - gastos operacionales - comisiones)
        $netProfit = $totalRevenue - $cogs - $totalOperationalCost - $gatewayFees;
        $margin    = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        $periodLabel = match ($this->filter) {
            '7d'  => 'últimos 7 días',
            '30d' => 'últimos 30 días',
            '12m' => 'últimos 12 meses',
            default => 'todo el tiempo',
        };

        return [
            Stat::make('Ingresos', number_format($totalRevenue, 2, ',', '.') . ' €')
                ->description($paidOrderCount . ' pedidos · ' . $periodLabel)
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart($this->getRevenueSparkline()),

            Stat::make('Coste de Producto (COGS)', number_format($cogs, 2, ',', '.') . ' €')
                ->description('Coste de los artículos vendidos')
                ->descriptionIcon('heroicon-m-cube')
                ->color('gray'),

            Stat::make('Packaging', number_format($packagingCost, 2, ',', '.') . ' €')
                ->description('Materiales de empaquetado')
                ->descriptionIcon('heroicon-m-gift-top')
                ->color('warning'),

            Stat::make('Envío / Mensajería', number_format($shippingCost, 2, ',', '.') . ' €')
                ->description('Coste pagado al transportista')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Comisiones Pasarela', number_format($gatewayFees, 2, ',', '.') . ' €')
                ->description('Estimación Stripe (~2.9 % + 0.25 €)')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('gray'),

            Stat::make('Beneficio Neto', number_format($netProfit, 2, ',', '.') . ' €')
                ->description(number_format($margin, 1) . ' % margen')
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->descriptionColor($netProfit >= 0 ? 'success' : 'danger')
                ->color($netProfit >= 0 ? 'success' : 'danger')
                ->chart($this->getProfitSparkline()),
        ];
    }

    // ── Sparklines ────────────────────────────────────────────────────────────

    private function getRevenueSparkline(): array
    {
        return $this->buildSparkline(fn ($d) =>
            (float) Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->whereDate('created_at', $d)->sum('total_amount')
        );
    }

    private function getProfitSparkline(): array
    {
        return $this->buildSparkline(function ($d) {
            $rev  = Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->whereDate('created_at', $d)->sum('total_amount');
            $cost = OrderItem::whereHas('order', fn ($q) => $q->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->whereDate('created_at', $d))
                ->selectRaw('SUM(cost_price_at_time * quantity) as total_cost')
                ->value('total_cost') ?? 0;
            $exp  = Expense::whereDate('expense_date', $d)->sum('amount');
            return (float) ($rev - $cost - $exp);
        });
    }

    private function buildSparkline(callable $resolver): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = $resolver(now()->subDays($i)->toDateString());
        }
        return $data;
    }
}
