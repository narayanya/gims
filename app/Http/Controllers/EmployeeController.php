<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        $query = DB::table('core_employee')
            ->select(
                'id',
                'emp_name',
                'employee_id',
                'emp_code',
                'emp_email',
                'emp_status',
                'emp_department',
                'emp_reporting'
            );

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('emp_name', 'like', "%{$search}%")
                ->orWhere('employee_id', 'like', "%{$search}%")
                ->orWhere('emp_code', 'like', "%{$search}%")
                ->orWhere('emp_email', 'like', "%{$search}%")
                ->orWhere('emp_department', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('id', 'desc')
            ->paginate(30)
            ->withQueryString();

        return view('master.employees.index', compact('employees'));
    }

    // AJAX Data
    public function getEmployees()
    {
        $employees = DB::table('users')
            ->select('id', 'name', 'emp_id', 'emp_code', 'email', 'status')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $employees
        ]);
    }

}
