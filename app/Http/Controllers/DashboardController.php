<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\InsightService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['from', 'to', 'equipment_id', 'category_id', 'region', 'customer_id', 'project_id', 'rental_status']);
        $analytics = AnalyticsService::for($filters);

        $kpis = $analytics->kpis();
        $revenueTrend = $analytics->revenueTrend(12);
        $equipmentStatus = $analytics->equipmentStatusDistribution();
        $utilizationTrend = $analytics->equipmentUtilizationTrend(12);
        $rentalFunnel = $analytics->rentalFunnel();
        $topEquipment = $analytics->topPerformingEquipment(6);
        $revenueByType = $analytics->revenueByEquipmentType(6);
        $revenueVsTarget = $analytics->revenueVsTarget(12);
        $paymentStatus = $analytics->paymentStatusDistribution();

        $recentRequests = \App\Models\RentalRequest::with('customer')->latest()->take(6)->get();
        $recentContracts = \App\Models\Contract::with('customer')->latest()->take(6)->get();

        return view('pages.admin.dashboard', compact(
            'kpis', 'revenueTrend', 'equipmentStatus', 'utilizationTrend', 'rentalFunnel',
            'topEquipment', 'revenueByType', 'revenueVsTarget', 'paymentStatus',
            'recentRequests', 'recentContracts', 'filters',
        ));
    }

    public function analytics(Request $request): View
    {
        $filters = $request->only(['from', 'to', 'equipment_id', 'category_id', 'region', 'customer_id', 'project_id']);
        $analytics = AnalyticsService::for($filters);

        return view('pages.admin.analytics', [
            'analytics' => $analytics,
            'filters' => $filters,
            'metrics' => $analytics->kpis(),
            'fleet' => $analytics,
            'insightsService' => app(InsightService::class),
        ]);
    }
}
