<?php

namespace App\Http\Controllers;    
use App\Models\SeedQuantity;
use Yajra\DataTables\Facades\DataTables;


use Illuminate\Http\Request;

class SeedQuantityController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SeedQuantity::with(['unit', 'lot', 'accession']);

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('unit', function ($row) {
                    return $row->unit->name ?? '-';
                })

                ->addColumn('lot', function ($row) {
                    return $row->lot->lot_no ?? '-';
                })

                ->addColumn('accession', function ($row) {
                    return $row->accession->accession_no ?? '-';
                })

                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-primary">Edit</button>';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('seed_quantities.index');
    }
}
