<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\EquipmentUtilization;
use App\Models\Invoice;
use App\Models\MaintenanceRecord;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\RentalRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function __construct(
        private ?Carbon $from = null,
        private ?Carbon $to = null,
        private ?int $equipmentId = null,
        private ?int $categoryId = null,
        private ?string $region = null,
        private ?int $customerId = null,
        private ?int $projectId = null,
    ) {
        $this->from = $this->from ?? Carbon::now()->subMonths(12)->startOfMonth();
        $this->to = $this->to ?? Carbon::now()->endOfDay();
    }

    public static function for(array $filters): static
    {
        return new static(
            from: ! empty($filters['from']) ? Carbon::parse($filters['from'])->startOfDay() : null,
            to: ! empty($filters['to']) ? Carbon::parse($filters['to'])->endOfDay() : null,
            equipmentId: $filters['equipment_id'] ?? null,
            categoryId: $filters['category_id'] ?? null,
            region: $filters['region'] ?? null,
            customerId: $filters['customer_id'] ?? null,
            projectId: $filters['project_id'] ?? null,
        );
    }

    public function months(int $count = 12): Collection
    {
        $months = collect();
        $cursor = $this->to->copy()->startOfMonth();
        for ($i = 0; $i < $count; $i++) {
            $months->prepend($cursor->copy());
            $cursor->subMonth();
        }

        return $months;
    }

    /**
     * Build a driver-agnostic "YYYY-MM" expression for the given date column.
     */
    private function monthExpr(string $column): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', $column)"
            : "DATE_FORMAT($column, '%Y-%m')";
    }

    private function equipmentQuery()
    {
        $q = Equipment::query();
        if ($this->equipmentId) {
            $q->where('id', $this->equipmentId);
        }
        if ($this->categoryId) {
            $q->where('category_id', $this->categoryId);
        }
        if ($this->region) {
            $q->where('region', $this->region);
        }

        return $q;
    }

    private function utilizationQuery()
    {
        $q = EquipmentUtilization::query()->whereBetween('date', [$this->from, $this->to]);
        if ($this->equipmentId) {
            $q->where('equipment_id', $this->equipmentId);
        }
        if ($this->categoryId) {
            $q->whereHas('equipment', fn ($e) => $e->where('category_id', $this->categoryId));
        }
        if ($this->region) {
            $q->whereHas('equipment', fn ($e) => $e->where('region', $this->region));
        }

        return $q;
    }

    public function kpis(): array
    {
        $totalFleet = $this->equipmentQuery()->count();
        $available = (clone $this->equipmentQuery())->where('status', 'available')->count();
        $rented = (clone $this->equipmentQuery())->where('status', 'rented')->count();
        $maintenance = (clone $this->equipmentQuery())->where('status', 'maintenance')->count();

        $utilizationTotal = (clone $this->utilizationQuery())->count();
        $utilizationRented = (clone $this->utilizationQuery())->where('status', 'rented')->count();
        $utilization = $utilizationTotal > 0 ? round(($utilizationRented / $utilizationTotal) * 100, 1) : 0;

        $prevFrom = $this->from->copy()->subMonths($this->from->diffInMonths($this->to) + 1);
        $prevTo = $this->from->copy()->subDay();

        $revenueThis = Payment::whereBetween('payment_date', [$this->from, $this->to])->sum('amount');
        $revenuePrev = Payment::whereBetween('payment_date', [$prevFrom, $prevTo])->sum('amount');

        $maintThis = MaintenanceRecord::whereBetween('date', [$this->from, $this->to])
            ->where('status', 'completed')->sum('cost');
        $maintPrev = MaintenanceRecord::whereBetween('date', [$prevFrom, $prevTo])
            ->where('status', 'completed')->sum('cost');

        $activeRentals = Contract::where('status', 'active')
            ->when($this->region, fn ($q, $r) => $q->whereHas('project', fn ($p) => $p->where('region', $r)))
            ->count();

        return [
            'total_fleet' => $totalFleet,
            'available' => $available,
            'rented' => $rented,
            'maintenance' => $maintenance,
            'active_rentals' => $activeRentals,
            'utilization' => $utilization,
            'revenue' => $revenueThis,
            'revenue_prev' => $revenuePrev,
            'revenue_trend' => $this->trendDirection($revenueThis, $revenuePrev),
            'maintenance_cost' => $maintThis,
            'maintenance_cost_prev' => $maintPrev,
            'maintenance_trend' => $this->trendDirection($maintThis, $maintPrev, invert: true),
        ];
    }

    private function trendDirection(float $current, float $previous, bool $invert = false): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        $pct = (($current - $previous) / $previous) * 100;

        return round($pct, 1);
    }

    public function revenueTrend(int $months = 12): array
    {
        $data = Payment::selectRaw($this->monthExpr('payment_date').' as ym, SUM(amount) as total')
            ->whereBetween('payment_date', [$this->from, $this->to])
            ->groupBy('ym')->get()->pluck('total', 'ym');

        $labels = [];
        $values = [];
        foreach ($this->months($months) as $m) {
            $key = $m->format('Y-m');
            $labels[] = $m->format('M Y');
            $values[] = round((float) ($data[$key] ?? 0), 2);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function revenueVsTarget(int $months = 12): array
    {
        $trend = $this->revenueTrend($months);
        $targets = [];
        $baseline = max(300000000, (float) collect($trend['values'])->max() * 1.15);
        foreach ($trend['labels'] as $i => $label) {
            $targets[] = round($baseline * (1 + $i * 0.02), 2);
        }

        return ['labels' => $trend['labels'], 'actual' => $trend['values'], 'target' => $targets];
    }

    public function rentalActivity(int $months = 12): array
    {
        $requests = RentalRequest::selectRaw($this->monthExpr('created_at').' as ym, COUNT(*) as c')
            ->whereBetween('created_at', [$this->from, $this->to])->groupBy('ym')->pluck('c', 'ym');
        $quotations = Quotation::selectRaw($this->monthExpr('created_at').' as ym, COUNT(*) as c')
            ->whereBetween('created_at', [$this->from, $this->to])->groupBy('ym')->pluck('c', 'ym');
        $contracts = Contract::selectRaw($this->monthExpr('created_at').' as ym, COUNT(*) as c')
            ->whereBetween('created_at', [$this->from, $this->to])->groupBy('ym')->pluck('c', 'ym');

        $out = ['labels' => [], 'requests' => [], 'quotations' => [], 'contracts' => []];
        foreach ($this->months($months) as $m) {
            $key = $m->format('Y-m');
            $out['labels'][] = $m->format('M Y');
            $out['requests'][] = (int) ($requests[$key] ?? 0);
            $out['quotations'][] = (int) ($quotations[$key] ?? 0);
            $out['contracts'][] = (int) ($contracts[$key] ?? 0);
        }

        return $out;
    }

    public function equipmentStatusDistribution(): array
    {
        $q = $this->equipmentQuery();

        return [
            'available' => (clone $q)->where('status', 'available')->count(),
            'rented' => (clone $q)->where('status', 'rented')->count(),
            'maintenance' => (clone $q)->where('status', 'maintenance')->count(),
            'unavailable' => (clone $q)->where('status', 'unavailable')->count(),
        ];
    }

    public function revenueByEquipmentType(int $limit = 8): array
    {
        $rows = EquipmentUtilization::selectRaw('equipment_id, SUM(revenue) as total')
            ->whereBetween('date', [$this->from, $this->to])
            ->groupBy('equipment_id')
            ->with('equipment.category')
            ->get()
            ->groupBy(fn ($u) => $u->equipment?->category?->name ?? 'Other')
            ->map(fn ($g) => (float) $g->sum('total'))
            ->sortDesc()
            ->take($limit);

        return ['labels' => $rows->keys()->values()->all(), 'values' => array_values($rows->toArray())];
    }

    public function topPerformingEquipment(int $limit = 10): array
    {
        $rows = EquipmentUtilization::selectRaw('equipment_id, SUM(revenue) as total, SUM(hours_operated) as hours, COUNT(*) as days')
            ->whereBetween('date', [$this->from, $this->to])
            ->where('status', 'rented')
            ->with('equipment.category')
            ->groupBy('equipment_id')
            ->get()
            ->sortByDesc('total')
            ->take($limit);

        return $rows->map(function ($r) {
            $equipment = $r->equipment;
            $rate = $equipment->daily_rate ?? 0;
            $capacityDays = $r->days * $rate;

            return [
                'code' => $equipment->equipment_code ?? '-',
                'name' => $equipment->name ?? '-',
                'category' => $equipment->category?->name ?? '-',
                'revenue' => round((float) $r->total, 2),
                'hours' => round((float) $r->hours, 0),
                'days' => (int) $r->days,
                'utilization' => $capacityDays > 0 ? round(((float) $r->total / $capacityDays) * 100, 1) : 0,
            ];
        })->all();
    }

    public function revenueByRegion(): array
    {
        $rows = EquipmentUtilization::selectRaw('equipment_id, SUM(revenue) as total')
            ->whereBetween('date', [$this->from, $this->to])
            ->groupBy('equipment_id')
            ->with('equipment')
            ->get()
            ->groupBy(fn ($u) => $u->equipment?->region ?? 'Unknown')
            ->map(fn ($g) => (float) $g->sum('total'))
            ->sortDesc();

        return ['labels' => $rows->keys()->values()->all(), 'values' => array_values($rows->toArray())];
    }

    public function maintenanceCostTrend(int $months = 12): array
    {
        $data = MaintenanceRecord::selectRaw($this->monthExpr('date').' as ym, SUM(cost) as total')
            ->where('status', 'completed')
            ->whereBetween('date', [$this->from, $this->to])
            ->groupBy('ym')->pluck('total', 'ym');

        $labels = [];
        $values = [];
        foreach ($this->months($months) as $m) {
            $labels[] = $m->format('M Y');
            $values[] = round((float) ($data[$m->format('Y-m')] ?? 0), 2);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function equipmentUtilizationTrend(int $months = 12): array
    {
        $data = EquipmentUtilization::selectRaw($this->monthExpr('date').' as ym, COUNT(*) as total, SUM(status = "rented") as rented')
            ->whereBetween('date', [$this->from, $this->to])
            ->groupBy('ym')->get()->keyBy('ym');

        $labels = [];
        $values = [];
        foreach ($this->months($months) as $m) {
            $key = $m->format('Y-m');
            $labels[] = $m->format('M Y');
            $row = $data[$key] ?? null;
            $values[] = $row && $row->total > 0 ? round(($row->rented / $row->total) * 100, 1) : 0;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function rentalFunnel(): array
    {
        $requests = RentalRequest::whereBetween('created_at', [$this->from, $this->to])->count();
        $quoted = Quotation::whereBetween('created_at', [$this->from, $this->to])->count();
        $contracts = Contract::whereBetween('created_at', [$this->from, $this->to])->count();
        $completed = Contract::whereBetween('created_at', [$this->from, $this->to])
            ->where('status', 'completed')->count();

        return [
            'requests' => $requests,
            'quotations' => $quoted,
            'contracts' => $contracts,
            'completed' => $completed,
        ];
    }

    public function paymentStatusDistribution(): array
    {
        return Invoice::query()
            ->selectRaw('payment_status, COUNT(*) as c')
            ->groupBy('payment_status')->pluck('c', 'payment_status')->all();
    }

    public function projectDistribution(): array
    {
        return Project::query()->selectRaw('industry, COUNT(*) as c')
            ->groupBy('industry')->pluck('c', 'industry')->all();
    }

    public function customerGrowth(int $months = 12): array
    {
        $data = Customer::selectRaw($this->monthExpr('created_at').' as ym, COUNT(*) as c')
            ->whereBetween('created_at', [$this->from, $this->to])
            ->groupBy('ym')->pluck('c', 'ym');

        $labels = [];
        $values = [];
        $running = 0;
        foreach ($this->months($months) as $m) {
            $labels[] = $m->format('M Y');
            $running += (int) ($data[$m->format('Y-m')] ?? 0);
            $values[] = $running;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function newVsReturningCustomers(): array
    {
        $recent = Carbon::now()->subDays(365);
        $returning = Payment::whereBetween('payment_date', [$this->from, $this->to])
            ->selectRaw('customer_id, COUNT(*) as c')
            ->groupBy('customer_id')->havingRaw('COUNT(*) >= 2')->count();
        $new = Payment::whereBetween('payment_date', [$this->from, $this->to])
            ->selectRaw('customer_id, COUNT(*) as c')
            ->groupBy('customer_id')->havingRaw('COUNT(*) = 1')->count();

        return ['new' => $new, 'returning' => $returning];
    }

    public function revenueByCustomer(int $limit = 8): array
    {
        $rows = Payment::selectRaw('customer_id, SUM(amount) as total')
            ->whereBetween('payment_date', [$this->from, $this->to])
            ->with('customer')
            ->groupBy('customer_id')
            ->get()
            ->sortByDesc('total')
            ->take($limit);

        return [
            'labels' => $rows->pluck('customer.company_name')->values()->all(),
            'values' => $rows->pluck('total')->values()->map(fn ($v) => round((float) $v, 2))->all(),
        ];
    }

    public function maintenanceByEquipment(int $limit = 10): array
    {
        $rows = MaintenanceRecord::whereBetween('date', [$this->from, $this->to])
            ->with('equipment')
            ->get()
            ->groupBy(fn ($m) => $m->equipment?->equipment_code ?? 'Unknown')
            ->map(fn ($g) => ['count' => $g->count(), 'cost' => (float) $g->sum('cost')])
            ->sortByDesc(fn ($v) => $v['count'])
            ->take($limit);

        return [
            'labels' => $rows->keys()->values()->all(),
            'counts' => $rows->pluck('count')->values()->all(),
            'costs' => $rows->pluck('cost')->values()->map(fn ($v) => round($v, 2))->all(),
        ];
    }

    public function rentalDurationDistribution(): array
    {
        $contracts = Contract::whereBetween('created_at', [$this->from, $this->to])
            ->get(['start_date', 'end_date']);

        $buckets = ['< 30 days' => 0, '30-60 days' => 0, '61-90 days' => 0, '91-180 days' => 0, '> 180 days' => 0];
        foreach ($contracts as $c) {
            $days = $c->start_date->diffInDays($c->end_date) + 1;
            match (true) {
                $days < 30 => $buckets['< 30 days']++,
                $days <= 60 => $buckets['30-60 days']++,
                $days <= 90 => $buckets['61-90 days']++,
                $days <= 180 => $buckets['91-180 days']++,
                default => $buckets['> 180 days']++,
            };
        }

        return $buckets;
    }

    public function profitabilityMatrix(): array
    {
        $equipment = Equipment::with(['utilization' => fn ($q) => $q->whereBetween('date', [$this->from, $this->to])])
            ->get();

        $rows = [];
        foreach ($equipment as $eq) {
            $u = $eq->utilization;
            if ($u->isEmpty()) {
                continue;
            }
            $revenue = (float) $u->sum('revenue');
            $maintenanceCost = (float) $eq->maintenanceRecords()
                ->whereBetween('date', [$this->from, $this->to])->sum('cost');
            $utilization = $eq->utilizationRate($this->from->toDateString(), $this->to->toDateString());
            $rows[] = [
                'code' => $eq->equipment_code,
                'name' => $eq->name,
                'revenue' => $revenue,
                'maintenance_cost' => $maintenanceCost,
                'utilization' => $utilization,
                'profit' => round($revenue - $maintenanceCost, 2),
                'margin' => $revenue > 0 ? round((($revenue - $maintenanceCost) / $revenue) * 100, 1) : 0,
            ];
        }

        usort($rows, fn ($a, $b) => $b['profit'] <=> $a['profit']);

        return $rows;
    }

    public function customerMetrics(): array
    {
        $total = Customer::count();
        $active = Customer::where('status', 'active')->count();

        $yearStart = Carbon::now()->startOfYear();
        $new = Customer::where('created_at', '>=', $yearStart)->count();

        $paymentCustomers = Payment::whereBetween('payment_date', [$this->from, $this->to])
            ->selectRaw('customer_id, COUNT(*) as c, SUM(amount) as total')
            ->groupBy('customer_id')->get();
        $returning = $paymentCustomers->filter(fn ($p) => $p->c >= 2)->count();
        $totalRentalValue = (float) $paymentCustomers->sum('total');
        $avg = $paymentCustomers->isNotEmpty() ? round($totalRentalValue / $paymentCustomers->count(), 2) : 0;
        $retention = $paymentCustomers->isNotEmpty() ? round(($returning / $paymentCustomers->count()) * 100, 1) : 0;

        return [
            'total' => $total,
            'active' => $active,
            'new' => $new,
            'returning' => $returning,
            'total_rental_value' => $totalRentalValue,
            'average_rental_value' => $avg,
            'rental_frequency' => $paymentCustomers->isNotEmpty() ? round($paymentCustomers->avg('c'), 1) : 0,
            'retention' => $retention,
        ];
    }

    public function customerSegments(): array
    {
        return Customer::query()->selectRaw('segment, COUNT(*) as c')
            ->groupBy('segment')->pluck('c', 'segment')->all();
    }

    public function financeSummary(): array
    {
        $revenue = (float) Payment::whereBetween('payment_date', [$this->from, $this->to])->sum('amount');
        $outstanding = Invoice::whereIn('payment_status', ['pending', 'partial', 'overdue'])
            ->get()->sum(fn ($i) => $i->balance);
        $paid = (float) Payment::whereBetween('payment_date', [$this->from, $this->to])
            ->whereHas('invoice', fn ($i) => $i->where('payment_status', 'paid'))->sum('amount');
        $overdue = Invoice::overdue()->get()->sum(fn ($i) => $i->balance);
        $maintCost = (float) MaintenanceRecord::whereBetween('date', [$this->from, $this->to])
            ->where('status', 'completed')->sum('cost');
        $operationalCost = $maintCost * 1.8;
        $profit = $revenue - $operationalCost;
        $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

        return [
            'revenue' => $revenue,
            'outstanding' => $outstanding,
            'paid' => $paid,
            'overdue' => $overdue,
            'maint_cost' => $maintCost,
            'operational_cost' => $operationalCost,
            'profit' => $profit,
            'margin' => $margin,
        ];
    }
}
