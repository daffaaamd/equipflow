<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function dashboard(Request $request): View
    {
        $filters = $request->only(['from', 'to']);
        $analytics = AnalyticsService::for($filters);
        $summary = $analytics->financeSummary();
        $revenueTrend = $analytics->revenueTrend(12);
        $paymentStatus = $analytics->paymentStatusDistribution();
        $recentPayments = Payment::with('customer', 'invoice')->latest()->take(8)->get();
        $overdueInvoices = Invoice::overdue()->with('customer')->latest()->take(8)->get();

        return view('pages.finance.dashboard', compact('summary', 'revenueTrend', 'paymentStatus', 'recentPayments', 'overdueInvoices', 'filters'));
    }
}