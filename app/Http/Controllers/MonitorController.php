<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitorController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'status', 'region']);

        $equipment = Equipment::with('category', 'assignedOperators')
            ->filter($filters)
            ->paginate(15)
            ->withQueryString();

        $dueService = Equipment::where('status', 'available')->get()
            ->filter(fn ($eq) => $eq->next_service_hours && ($eq->next_service_hours - $eq->operating_hours) < 300)->count();

        $available = Equipment::where('status', 'available')->count();
        $rented = Equipment::where('status', 'rented')->count();
        $inMaintenance = Equipment::where('status', 'maintenance')->count();

        $categories = \App\Models\EquipmentCategory::orderBy('sort_order')->get();

        return view('pages.monitoring.index', compact('equipment', 'filters', 'dueService', 'available', 'rented', 'inMaintenance', 'categories'));
    }

    public function show(Equipment $equipment): View
    {
        $equipment->load('category', 'images', 'maintenanceRecords', 'assignedOperators', 'utilization');

        return view('pages.monitoring.show', compact('equipment'));
    }
}
