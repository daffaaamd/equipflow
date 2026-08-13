<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Invoice;
use App\Models\MaintenanceRecord;
use App\Models\Payment;
use App\Models\Project;
use App\Models\RentalRequest;
use App\Models\EquipmentUtilization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(): View
    {
        $reports = [
            ['key' => 'rental', 'name' => 'Rental Report', 'description' => 'Rental requests, quotations, and contracts activity over a period.'],
            ['key' => 'utilization', 'name' => 'Fleet Utilization Report', 'description' => 'Equipment utilization rates, rental days, and operating hours.'],
            ['key' => 'revenue', 'name' => 'Revenue Report', 'description' => 'Invoiced revenue, collections, and outstanding balances.'],
            ['key' => 'maintenance', 'name' => 'Maintenance Report', 'description' => 'Maintenance activities, costs, and equipment downtime.'],
            ['key' => 'customer', 'name' => 'Customer Report', 'description' => 'Customer accounts, segments, and rental value.'],
            ['key' => 'project', 'name' => 'Project Report', 'description' => 'Project portfolio, status, and deployed equipment.'],
            ['key' => 'profitability', 'name' => 'Profitability Report', 'description' => 'Equipment revenue, costs, and profit margins.'],
        ];

        return view('pages.admin.reports.index', compact('reports'));
    }

    public function show(Request $request, string $report): View
    {
        abort_if(! in_array($report, ['rental', 'utilization', 'revenue', 'maintenance', 'customer', 'project', 'profitability']), 404);

        $filters = $request->only(['from', 'to', 'equipment_id', 'category_id', 'region', 'customer_id', 'project_id']);
        $from = $filters['from'] ?? now()->subMonths(12)->startOfMonth()->toDateString();
        $to = $filters['to'] ?? now()->toDateString();

        $rows = $this->rows($report, $filters, $from, $to);
        $columns = $this->columns($report);

        $reportMeta = collect([
            ['key' => 'rental', 'name' => 'Rental Report'],
            ['key' => 'utilization', 'name' => 'Fleet Utilization Report'],
            ['key' => 'revenue', 'name' => 'Revenue Report'],
            ['key' => 'maintenance', 'name' => 'Maintenance Report'],
            ['key' => 'customer', 'name' => 'Customer Report'],
            ['key' => 'project', 'name' => 'Project Report'],
            ['key' => 'profitability', 'name' => 'Profitability Report'],
        ])->firstWhere('key', $report);

        return view('pages.admin.reports.show', compact('report', 'reportMeta', 'rows', 'columns', 'filters', 'from', 'to'));
    }

    private function columns(string $report): array
    {
        return match ($report) {
            'rental' => ['Request Number', 'Customer', 'Project', 'Status', 'Created'],
            'utilization' => ['Equipment', 'Category', 'Days', 'Rented Days', 'Utilization %', 'Revenue'],
            'revenue' => ['Invoice Number', 'Customer', 'Date', 'Due Date', 'Total', 'Status'],
            'maintenance' => ['Maintenance No', 'Equipment', 'Type', 'Date', 'Cost', 'Downtime (h)', 'Status'],
            'customer' => ['Customer', 'Code', 'Segment', 'Region', 'Active Contracts', 'Rental Value'],
            'project' => ['Project', 'Customer', 'Industry', 'Region', 'Status', 'Value'],
            'profitability' => ['Equipment', 'Code', 'Revenue', 'Maintenance Cost', 'Profit', 'Margin %'],
            default => [],
        };
    }

    private function rows(string $report, array $filters, string $from, string $to): array
    {
        return match ($report) {
            'rental' => RentalRequest::with('customer')->whereBetween('created_at', [$from, $to])
                ->get()->map(fn ($r) => [$r->request_number, $r->customer->company_name, $r->project_name, $r->status, $r->created_at->format('d M Y')])->all(),
            'utilization' => EquipmentUtilization::with('equipment.category')
                ->whereBetween('date', [$from, $to])
                ->get()->groupBy('equipment_id')
                ->map(function ($g) {
                    $eq = $g->first()->equipment;
                    $days = $g->count();
                    $rented = $g->where('status', 'rented')->count();
                    $revenue = $g->sum('revenue');

                    return [$eq->equipment_code, $eq->category->name ?? '-', $days, $rented, $days > 0 ? round(($rented / $days) * 100, 1) : 0, number_format($revenue)];
                })->values()->all(),
            'revenue' => Invoice::with('customer')->whereBetween('invoice_date', [$from, $to])
                ->get()->map(fn ($i) => [$i->invoice_number, $i->customer->company_name, $i->invoice_date->format('d M Y'), $i->due_date->format('d M Y'), number_format($i->total), $i->payment_status])->all(),
            'maintenance' => MaintenanceRecord::with('equipment')->whereBetween('date', [$from, $to])
                ->get()->map(fn ($m) => [$m->maintenance_number, $m->equipment->equipment_code, $m->type, $m->date->format('d M Y'), number_format($m->cost), $m->downtime_hours, $m->status])->all(),
            'customer' => Customer::withCount(['contracts' => fn ($q) => $q->where('status', 'active')])->get()
                ->map(fn ($c) => [$c->company_name, $c->customer_code, $c->segment, $c->region, $c->contracts_count, number_format($c->total_rental_value)])->all(),
            'project' => Project::with('customer')->whereBetween('created_at', [$from, $to])
                ->get()->map(fn ($p) => [$p->name, $p->customer->company_name, $p->industry, $p->region, $p->status, number_format($p->contract_value)])->all(),
            'profitability' => collect((new \App\Services\AnalyticsService(equipmentId: $filters['equipment_id'] ?? null, categoryId: $filters['category_id'] ?? null, region: $filters['region'] ?? null))->profitabilityMatrix())
                ->map(fn ($p) => [$p['name'], $p['code'], number_format($p['revenue']), number_format($p['maintenance_cost']), number_format($p['profit']), $p['margin']])->all(),
            default => [],
        };
    }

    public function export(Request $request, string $report)
    {
        abort_if(! in_array($report, ['rental', 'utilization', 'revenue', 'maintenance', 'customer', 'project', 'profitability']), 404);

        $filters = $request->only(['from', 'to']);
        $from = $filters['from'] ?? now()->subMonths(12)->startOfMonth()->toDateString();
        $to = $filters['to'] ?? now()->toDateString();

        $rows = $this->rows($report, $filters, $from, $to);
        $columns = $this->columns($report);

        $filename = "equipflow-{$report}-report.csv";

        $callback = function () use ($columns, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }
}
