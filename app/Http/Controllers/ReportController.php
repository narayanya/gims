<?php

namespace App\Http\Controllers;
use App\Models\SeedRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use App\Models\Accession;

use Illuminate\Http\Request;
use  Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::now();   

        return view('report.reports', compact('today'));
    }

    // View Request Report
    public function requestReport(Request $request)
    {
        $query = SeedRequest::with(['user', 'crop', 'variety', 'unit']);
        

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->latest()->get();

        return view('report.request_report', compact('requests'));
    }

    // Download CSV
    public function downloadRequestReport(Request $request)
    {
        $fileName = 'request_report.csv';

        $requests = SeedRequest::with(['user', 'crop', 'variety', 'unit'])->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $columns = [
            'Request No',
            'User',
            'Crop',
            'Variety',
            'Quantity',
            'Unit',
            'Status',
            'Date'
        ];

        $callback = function () use ($requests, $columns) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $columns);

            foreach ($requests as $row) {
                fputcsv($file, [
                    $row->request_number,
                    $row->user->name ?? '',
                    $row->crop->name ?? '',
                    $row->variety->name ?? '',
                    $row->quantity,
                    $row->unit->name ?? '',
                    $row->status,
                    $row->created_at,
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function expiryReport()
    {
        $accessions = Accession::with(['crop', 'storageTime'])
            ->whereNotNull('expiry_date')
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('report.expiry-report', compact('accessions'));
    }

    public function downloadExpiryReport()
    {
        $accessions = Accession::with(['crop', 'storageTime'])
            ->whereNotNull('expiry_date')
            ->orderBy('expiry_date')
            ->get();

        $filename = "expiry_report.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($accessions) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Accession',
                'Crop',
                'Storage',
                'Recheck Date',
                'Expiry Date'
            ]);

            foreach ($accessions as $a) {
                fputcsv($file, [
                    $a->accession_name ?? $a->accession_number,
                    $a->crop->crop_name ?? '',
                    $a->storageTime->code ?? '',
                    $a->recheck_date,
                    $a->expiry_date,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Transaction Reports ───────────────────────────────────────────────

    private function transactionReportQuery(string $type, Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        switch ($type) {
            case 'arrival':
                // Accessions with acc_source = external (arrived from outside)
                $query = Accession::with(['crop', 'variety', 'warehouse'])
                    ->where('acc_source', 'external');
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('created_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'accessioning':
                $query = Accession::with(['crop', 'variety', 'warehouse', 'storageType']);
                if ($dateFrom) $query->whereDate('entry_date', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('entry_date', '<=', $dateTo);
                return $query->latest()->get();

            case 'request':
                $query = SeedRequest::with(['user', 'crop', 'variety']);
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('created_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'dispatch':
                // Approved/dispatched requests
                $query = SeedRequest::with(['user', 'crop', 'variety'])
                    ->whereIn('status', ['approved', 'dispatched', 'completed']);
                if ($dateFrom) $query->whereDate('approved_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('approved_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'return':
                $query = SeedRequest::with(['user', 'crop', 'variety'])
                    ->where('status', 'returned');
                if ($dateFrom) $query->whereDate('updated_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('updated_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'regeneration':
                // Lots created for regeneration (lot type name contains 'regen')
                $query = \App\Models\Lot::with(['accession', 'crop', 'lotType', 'storage'])
                    ->whereHas('lotType', fn($q) => $q->where('name', 'like', '%regen%'))
                    ->orWhere('description', 'like', '%regen%');
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('created_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'disposal':
                $query = Accession::with(['crop', 'variety'])
                    ->where('status', 'depleted');
                if ($dateFrom) $query->whereDate('updated_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('updated_at', '<=', $dateTo);
                return $query->latest()->get();
        }
        return collect();
    }

    public function transactionReport(Request $request, string $type)
    {
        $titles = [
            'arrival'       => 'Arrival Transaction',
            'accessioning'  => 'Accessioning Transaction',
            'request'       => 'Request Transaction',
            'dispatch'      => 'Dispatch Transaction',
            'return'        => 'Return Transaction',
            'regeneration'  => 'Regeneration Transaction',
            'disposal'      => 'Disposal Transaction',
        ];

        if (!array_key_exists($type, $titles)) abort(404);

        $records   = $this->transactionReportQuery($type, $request);
        $title     = $titles[$type];
        $dateFrom  = $request->date_from;
        $dateTo    = $request->date_to;

        return view('report.transaction-report', compact('records', 'title', 'type', 'dateFrom', 'dateTo'));
    }

    public function downloadTransactionReport(Request $request, string $type)
    {
        $titles = [
            'arrival'       => 'Arrival Transaction',
            'accessioning'  => 'Accessioning Transaction',
            'request'       => 'Request Transaction',
            'dispatch'      => 'Dispatch Transaction',
            'return'        => 'Return Transaction',
            'regeneration'  => 'Regeneration Transaction',
            'disposal'      => 'Disposal Transaction',
        ];

        if (!array_key_exists($type, $titles)) abort(404);

        $records  = $this->transactionReportQuery($type, $request);
        $filename = $type . '_transaction_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($records, $type) {
            $file = fopen('php://output', 'w');

            // Dynamic columns per type
            if (in_array($type, ['arrival', 'accessioning', 'disposal'])) {
                fputcsv($file, ['Accession No', 'Accession Name', 'Crop', 'Variety', 'Quantity', 'Warehouse', 'Status', 'Date']);
                foreach ($records as $r) {
                    fputcsv($file, [
                        $r->accession_number,
                        $r->accession_name,
                        $r->crop?->crop_name ?? '',
                        $r->variety?->variety_name ?? '',
                        $r->quantity,
                        $r->warehouse?->name ?? '',
                        $r->status,
                        $r->created_at?->format('Y-m-d'),
                    ]);
                }
            } elseif (in_array($type, ['request', 'dispatch', 'return'])) {
                fputcsv($file, ['Request No', 'Requester', 'Crop', 'Variety', 'Quantity', 'Status', 'Date']);
                foreach ($records as $r) {
                    fputcsv($file, [
                        $r->request_number,
                        $r->user?->name ?? $r->requester_name ?? '',
                        $r->crop?->crop_name ?? '',
                        $r->variety?->variety_name ?? '',
                        $r->quantity,
                        $r->status,
                        $r->created_at?->format('Y-m-d'),
                    ]);
                }
            } elseif ($type === 'regeneration') {
                fputcsv($file, ['Lot Number', 'Accession', 'Crop', 'Lot Type', 'Quantity', 'Storage', 'Status', 'Date']);
                foreach ($records as $r) {
                    fputcsv($file, [
                        $r->lot_number,
                        $r->accession?->accession_number ?? '',
                        $r->crop?->crop_name ?? '',
                        $r->lotType?->name ?? '',
                        $r->quantity,
                        $r->storage?->name ?? '',
                        $r->status,
                        $r->created_at?->format('Y-m-d'),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
