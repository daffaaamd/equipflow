<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    private function resolve(Request $request): AnalyticsService
    {
        return AnalyticsService::for($request->only(['from', 'to', 'equipment_id', 'category_id', 'region', 'customer_id', 'project_id']));
    }

    public function fleet(Request $request): View
    {
        $a = $this->resolve($request);

        return view('pages.admin.analytics.fleet', [
            'filters' => $request->only(['from', 'to', 'equipment_id', 'category_id', 'region', 'customer_id', 'project_id']),
            'kpis' => $a->kpis(),
            'equipmentStatus' => $a->equipmentStatusDistribution(),
            'utilizationTrend' => $a->equipmentUtilizationTrend(12),
            'revenueByType' => $a->revenueByEquipmentType(8),
            'topEquipment' => $a->topPerformingEquipment(10),
            'profitability' => collect($a->profitabilityMatrix())->take(12),
        ]);
    }

    public function rental(Request $request): View
    {
        $a = $this->resolve($request);

        return view('pages.admin.analytics.rental', [
            'filters' => $request->only(['from', 'to', 'equipment_id', 'category_id', 'region', 'customer_id', 'project_id']),
            'rentalActivity' => $a->rentalActivity(12),
            'rentalFunnel' => $a->rentalFunnel(),
            'duration' => $a->rentalDurationDistribution(),
            'revenueTrend' => $a->revenueTrend(12),
        ]);
    }

    public function maintenance(Request $request): View
    {
        $a = $this->resolve($request);

        $dueCount = \App\Models\Equipment::where('status', 'available')->get()
            ->filter(fn ($eq) => $eq->next_service_hours && ($eq->next_service_hours - $eq->operating_hours) < 500)->count();

        return view('pages.admin.analytics.maintenance', [
            'filters' => $request->only(['from', 'to', 'equipment_id', 'category_id', 'region', 'customer_id', 'project_id']),
            'costTrend' => $a->maintenanceCostTrend(12),
            'maintenanceByEquipment' => $a->maintenanceByEquipment(10),
            'dueCount' => $dueCount,
            'activeMaintenance' => \App\Models\MaintenanceRecord::whereIn('status', ['scheduled', 'in_progress'])->count(),
            'totalCost' => \App\Models\MaintenanceRecord::where('status', 'completed')->sum('cost'),
            'totalDowntime' => \App\Models\MaintenanceRecord::whereIn('status', ['completed', 'in_progress'])->sum('downtime_hours'),
            'frequency' => \App\Models\MaintenanceRecord::count(),
        ]);
    }

    public function customer(Request $request): View
    {
        $a = $this->resolve($request);

        return view('pages.admin.analytics.customer', [
            'filters' => $request->only(['from', 'to', 'equipment_id', 'category_id', 'region', 'customer_id', 'project_id']),
            'metrics' => $a->customerMetrics(),
            'segments' => $a->customerSegments(),
            'growth' => $a->customerGrowth(12),
            'newVsReturning' => $a->newVsReturningCustomers(),
            'revenueByCustomer' => $a->revenueByCustomer(8),
        ]);
    }

    public function finance(Request $request): View
    {
        $a = $this->resolve($request);

        return view('pages.admin.analytics.finance', [
            'filters' => $request->only(['from', 'to', 'equipment_id', 'category_id', 'region', 'customer_id', 'project_id']),
            'summary' => $a->financeSummary(),
            'revenueTrend' => $a->revenueTrend(12),
            'revenueVsTarget' => $a->revenueVsTarget(12),
            'paymentStatus' => $a->paymentStatusDistribution(),
            'revenueByRegion' => $a->revenueByRegion(),
            'revenueByType' => $a->revenueByEquipmentType(8),
        ]);
    }
}
