<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LogReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Separate page visits from action logs
        $tab = $request->get('tab', 'actions');

        if ($tab === 'visits') {
            $query->where('action', 'page_visit');
        } else {
            $query->where('action', '!=', 'page_visit');
        }

        $logs    = $query->paginate(20)->withQueryString();
        $users   = User::orderBy('name')->get();

        $knownModules = ['accession', 'auth', 'crop', 'lot', 'request', 'storage', 'variety'];
        $dbModules    = ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module')->toArray();
        $modules      = collect(array_unique(array_merge($knownModules, $dbModules)))->sort()->values();

        $actions = ActivityLog::select('action')
            ->where('action', '!=', 'page_visit')
            ->distinct()->orderBy('action')->pluck('action');

        return view('logs.index', compact('logs', 'users', 'modules', 'actions', 'tab'));
    }

    public function pageExit(Request $request)
    {
        // sendBeacon sends JSON body, not form data
        $data  = $request->json()->all() ?: $request->all();
        $logId = $data['log_id'] ?? null;

        if (!$logId) return response()->json(['ok' => false]);

        $log = ActivityLog::where('id', $logId)
            ->where('user_id', auth()->id())
            ->whereNull('out_time')
            ->first();

        if ($log && $log->in_time) {
            $now   = now();
            $spent = max(0, min((int) $log->in_time->diffInSeconds($now), 86400));
            $log->update([
                'out_time'           => $now,
                'time_spent_seconds' => $spent,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return view('logs.show', compact('log'));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with('user')->latest('created_at');

        if ($request->filled('user_id'))  $query->where('user_id', $request->user_id);
        if ($request->filled('module'))   $query->where('module', $request->module);
        if ($request->filled('action'))   $query->where('action', $request->action);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->get();

        $filename = 'activity_logs_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'User', 'Action', 'Module', 'Record ID', 'Record Label', 'IP Address', 'Date/Time']);
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->action,
                    $log->module,
                    $log->record_id,
                    $log->record_label,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
