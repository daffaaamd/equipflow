<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::with('user')
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('action', 'like', "%{$s}%")
                    ->orWhere('entity_type', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            }))
            ->when($request->user_id, fn ($q, $v) => $q->where('user_id', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['search', 'user_id']);
        $users = \App\Models\User::orderBy('name')->get();

        return view('pages.audit.index', compact('logs', 'filters', 'users'));
    }
}