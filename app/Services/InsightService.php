<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Equipment;
use App\Models\Invoice;
use App\Models\MaintenanceRecord;
use App\Models\Operator;
use Carbon\Carbon;

class InsightService
{
    public function __construct(private AnalyticsService $analytics)
    {
    }

    public function insights(): array
    {
        $insights = [];

        $kpis = $this->analytics->kpis();

        // High fleet utilization
        $highUtilization = Equipment::query()
            ->withCount(['utilization as total_days'])
            ->withCount(['utilization as rented_days' => fn ($q) => $q->where('status', 'rented')])
            ->having('total_days', '>', 0)
            ->get()
            ->map(function ($eq) {
                return [
                    'code' => $eq->equipment_code,
                    'name' => $eq->name,
                    'rate' => $eq->total_days > 0 ? round(($eq->rented_days / $eq->total_days) * 100, 1) : 0,
                ];
            })
            ->filter(fn ($e) => $e['rate'] >= 85)
            ->sortByDesc('rate')
            ->take(3);

        foreach ($highUtilization as $eq) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'High Fleet Utilization',
                'message' => "{$eq['code']} ({$eq['name']}) has maintained utilization above 85% during the selected period.",
            ];
        }

        // Low utilization
        $lowUtilization = Equipment::query()
            ->withCount(['utilization as total_days'])
            ->withCount(['utilization as rented_days' => fn ($q) => $q->where('status', 'rented')])
            ->having('total_days', '>', 0)
            ->get()
            ->map(fn ($eq) => [
                'code' => $eq->equipment_code,
                'name' => $eq->name,
                'rate' => $eq->total_days > 0 ? round(($eq->rented_days / $eq->total_days) * 100, 1) : 0,
            ])
            ->filter(fn ($e) => $e['rate'] < 35)
            ->sortBy('rate')
            ->take(3);

        foreach ($lowUtilization as $eq) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Low Fleet Utilization',
                'message' => "{$eq['code']} ({$eq['name']}) has remained underutilized during the selected period.",
            ];
        }

        // Maintenance approaching
        $due = Equipment::where('status', 'available')
            ->whereNotNull('next_service_hours')
            ->get()
            ->filter(fn ($eq) => ($eq->next_service_hours - $eq->operating_hours) < 200)
            ->take(3);

        foreach ($due as $eq) {
            $remaining = round($eq->next_service_hours - $eq->operating_hours, 0);
            $insights[] = [
                'type' => 'critical',
                'title' => 'Maintenance Alert',
                'message' => "{$eq->equipment_code} is approaching its scheduled service threshold ({$remaining} hours remaining).",
            ];
        }

        // Revenue opportunity - demand by category
        $demand = \App\Models\RentalRequestItem::query()
            ->with('category')
            ->get()
            ->groupBy(fn ($i) => $i->category?->name ?? 'Unknown')
            ->map->count()
            ->sortDesc()
            ->take(3);

        if ($demand->isNotEmpty()) {
            $insights[] = [
                'type' => 'opportunity',
                'title' => 'Revenue Opportunity',
                'message' => "Demand for {$demand->keys()->first()} is higher than other equipment categories. Consider expanding this fleet segment.",
            ];
        }

        // Payment risk
        $overdueCount = Invoice::overdue()->count();
        if ($overdueCount > 0) {
            $amount = Invoice::overdue()->get()->sum(fn ($i) => $i->balance);
            $insights[] = [
                'type' => 'critical',
                'title' => 'Payment Risk',
                'message' => "{$overdueCount} customer invoices are overdue with a combined balance of " . number_format($amount) . '.',
            ];
        }

        // Active contracts
        $expiring = Contract::where('status', 'active')
            ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->count();
        if ($expiring > 0) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Contract Expiry',
                'message' => "{$expiring} active contracts are due to expire within the next 30 days. Review renewals.",
            ];
        }

        // Operator certification
        $expiringCert = Operator::whereNotNull('certification_expiry')
            ->where('status', 'active')
            ->get()->filter(fn ($o) => $o->isCertificationExpiring())->count();
        if ($expiringCert > 0) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Certification Renewal',
                'message' => "{$expiringCert} operators have certifications expiring within 60 days.",
            ];
        }

        if (empty($insights)) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'Fleet Operations Nominal',
                'message' => 'No significant operational exceptions detected during the selected period.',
            ];
        }

        return $insights;
    }

    public function recommendations(): array
    {
        $recs = [];

        $kpis = $this->analytics->kpis();

        $rentedCount = Equipment::where('status', 'rented')->count();
        $availableCount = Equipment::where('status', 'available')->count();

        // Excavator demand
        $excavatorDemand = \App\Models\RentalRequestItem::query()
            ->whereHas('category', fn ($q) => $q->where('name', 'like', '%Excavator%'))
            ->count();
        $availableExcavators = Equipment::where('status', 'available')
            ->whereHas('category', fn ($q) => $q->where('name', 'like', '%Excavator%'))
            ->count();

        if ($excavatorDemand > 0 && $availableExcavators <= 2) {
            $recs[] = ['priority' => 'high', 'title' => 'Increase Excavator Availability', 'action' => 'Review excavator maintenance turnaround and consider fleet expansion to meet demand.'];
        } else {
            $recs[] = ['priority' => 'medium', 'title' => 'Balance Excavator Demand', 'action' => 'Monitor excavator demand versus availability across regions.'];
        }

        // Underutilized equipment
        $underutilized = Equipment::query()
            ->withCount(['utilization as total_days'])
            ->withCount(['utilization as rented_days' => fn ($q) => $q->where('status', 'rented')])
            ->having('total_days', '>', 0)
            ->get()
            ->filter(fn ($eq) => $eq->total_days > 0 && ($eq->rented_days / $eq->total_days) < 0.3)
            ->take(3);

        if ($underutilized->isNotEmpty()) {
            $codes = $underutilized->pluck('equipment_code')->implode(', ');
            $recs[] = ['priority' => 'high', 'title' => 'Redeploy Underutilized Equipment', 'action' => "Equipment {$codes} has below 30% utilization. Consider relocating to regions with higher demand."];
        }

        // Preventive maintenance
        $dueMaintenance = Equipment::where('status', 'available')->get()
            ->filter(fn ($eq) => $eq->next_service_hours && ($eq->next_service_hours - $eq->operating_hours) < 300)
            ->count();
        if ($dueMaintenance > 0) {
            $recs[] = ['priority' => 'high', 'title' => 'Schedule Preventive Maintenance', 'action' => "{$dueMaintenance} units are approaching service thresholds. Schedule preventive maintenance to avoid unplanned downtime."];
        }

        // Overdue payments
        $overdue = Invoice::overdue()->count();
        if ($overdue > 0) {
            $recs[] = ['priority' => 'high', 'title' => 'Review Overdue Payments', 'action' => "Follow up on {$overdue} overdue invoices. Prioritize high-value accounts with partial payments."];
        }

        // Region targeting
        $regionRevenue = $this->analytics->revenueByRegion();
        if (! empty($regionRevenue['labels'])) {
            $topRegion = $regionRevenue['labels'][0];
            $recs[] = ['priority' => 'medium', 'title' => 'Target High Demand Regions', 'action' => "Concentrate marketing and fleet deployment in {$topRegion}, the top revenue region."];
        }

        // Customer retention
        $retention = $this->analytics->customerMetrics()['retention'];
        if ($retention < 50) {
            $recs[] = ['priority' => 'medium', 'title' => 'Improve Customer Retention', 'action' => "Retention is at {$retention}%. Implement loyalty pricing for repeat rental customers."];
        }

        // Low utilization equipment category
        $util = $this->analytics->equipmentUtilizationTrend(6);
        $recentAvg = collect(array_slice($util['values'], -3))->avg();
        if ($recentAvg < 50) {
            $recs[] = ['priority' => 'low', 'title' => 'Stimulate Off-Peak Demand', 'action' => 'Fleet utilization is below 50% in recent months. Offer project-based packages for idle equipment.'];
        }

        return $recs;
    }
}
