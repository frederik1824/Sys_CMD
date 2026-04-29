<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filtros opcionales
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('model')) {
            $query->where('model_type', 'like', '%' . $request->model . '%');
        }

        $logs = $query->paginate(50)->withQueryString();
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.audit.index', compact('logs', 'users'));
    }
}
