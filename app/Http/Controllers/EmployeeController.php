<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{

    public function index()
    {
         $employees = DB::table('core_employee')
        ->select('id','emp_name','employee_id','emp_code','emp_email','emp_status', 'emp_department', 'emp_reporting')
        ->orderBy('id','desc')
        ->paginate(20);

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
