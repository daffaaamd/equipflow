<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\InsightService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InsightsController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['from', 'to', 'equipment_id', 'category_id', 'region', 'customer_id', 'project_id']);
        $analytics = AnalyticsService::for($filters);
        $insights = app(InsightService::class);

        return view('pages.admin.insights', [
            'filters' => $filters,
            'insights' => $insights->insights(),
            'recommendations' => $insights->recommendations(),
            'kpis' => $analytics->kpis(),
        ]);
    }
}
