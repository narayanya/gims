<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PageVisitLogger
{
    // Routes to skip (assets, ajax, auth pages, etc.)
    protected array $skip = [
        'login', 'logout', 'password/*', 'register',
        'get-*', 'check-*', 'employee/*', 'accession/*',
        'get-lots-by-warehouse', 'get-storages-by-warehouse',
        'get-warehouse-by-storage', 'logs/page-exit',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only track authenticated GET requests (page views)
        if (!Auth::check() || !$request->isMethod('GET') || $request->ajax()) {
            return $response;
        }

        // Skip non-HTML responses
        $contentType = $response->headers->get('Content-Type', '');
        if (str_contains($contentType, 'json') || str_contains($contentType, 'csv')) {
            return $response;
        }

        // Skip matched patterns
        $path = $request->path();
        foreach ($this->skip as $pattern) {
            if (fnmatch($pattern, $path)) {
                return $response;
            }
        }

        $now     = now();
        $userId  = Auth::id();
        $pageUrl = $request->fullUrl();
        $title   = $this->resolveTitle($path);

        // Close out the previous page visit for this user
        $prev = Session::get('page_visit_log_id');
        if ($prev) {
            $prevLog = ActivityLog::where('id', $prev)->whereNull('out_time')->first();
            if ($prevLog && $prevLog->in_time) {
                $spent = max(0, min((int) $prevLog->in_time->diffInSeconds($now), 86400));
                $prevLog->update([
                    'out_time'           => $now,
                    'time_spent_seconds' => $spent,
                ]);
            }
        }

        // Create new page visit entry
        $log = ActivityLog::create([
            'user_id'    => $userId,
            'action'     => 'page_visit',
            'module'     => 'navigation',
            'page_url'   => $pageUrl,
            'page_title' => $title,
            'in_time'    => $now,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => $now,
        ]);

        Session::put('page_visit_log_id', $log->id);

        return $response;
    }

    protected function resolveTitle(string $path): string
    {
        $map = [
            ''                          => 'Dashboard',
            'home'                      => 'Dashboard',
            'accession-list'            => 'Accession List',
            'accessionform'             => 'New Accession',
            'lot-management'            => 'Lot Management',
            'lot-management/create'     => 'New Lot',
            'warehouse-transfer'        => 'Warehouse Transfer',
            'dispatch-orders'           => 'Dispatch Management',
            'requests'                  => 'Requests',
            'requests/create'           => 'New Request',
            'storage-management'        => 'Storage Management',
            'warehouses'                => 'Warehouses',
            'crops'                     => 'Crops',
            'varieties'                 => 'Varieties',
            'logs'                      => 'Activity Logs',
            'settings'                  => 'Settings',
            'users'                     => 'Users',
        ];

        // Exact match first
        if (isset($map[$path])) {
            return $map[$path];
        }

        // Prefix match
        foreach ($map as $prefix => $label) {
            if ($prefix && str_starts_with($path, $prefix)) {
                return $label;
            }
        }

        // Fallback: humanise the path
        $parts = explode('/', $path);
        return ucwords(str_replace(['-', '_'], ' ', end($parts)));
    }
}
