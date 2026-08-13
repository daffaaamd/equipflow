<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::with('customer')
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('project_code', 'like', "%{$s}%");
            }))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->industry, fn ($q, $v) => $q->where('industry', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['search', 'status', 'industry']);

        return view('pages.projects.index', compact('projects', 'filters'));
    }

    public function create(): View
    {
        $customers = Customer::where('status', 'active')->orderBy('company_name')->get();

        return view('pages.projects.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'customer_id' => ['required', 'exists:customers,id'],
            'industry' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'contract_value' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:planning,active,completed,on_hold,cancelled'],
            'description' => ['nullable', 'string'],
        ]);

        $project = Project::create(array_merge($data, [
            'project_code' => 'PRJ-' . strtoupper(substr($data['industry'], 0, 3)) . '-' . str_pad((string) (Project::max('id') + 1), 4, '0', STR_PAD_LEFT),
        ]));

        ActivityService::log('create', 'project', $project->id, "Project {$project->name} created");

        return redirect()->route('admin.projects.show', $project->id)->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $project->load('customer', 'contracts', 'rentalRequests', 'deliveries');

        return view('pages.projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $customers = Customer::orderBy('company_name')->get();

        return view('pages.projects.edit', compact('project', 'customers'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'customer_id' => ['required', 'exists:customers,id'],
            'industry' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'contract_value' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:planning,active,completed,on_hold,cancelled'],
            'description' => ['nullable', 'string'],
        ]);

        $project->update($data);
        ActivityService::log('update', 'project', $project->id, "Project {$project->name} updated");

        return redirect()->route('admin.projects.show', $project->id)->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        ActivityService::log('delete', 'project', $project->id, "Project {$project->name} removed");
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project removed successfully.');
    }
}
