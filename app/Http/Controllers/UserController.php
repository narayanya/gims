<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->where('status', 1)->paginate(10);
        $roles = Role::all();

        $employees = DB::table('core_employee')
            ->select('employee_id','emp_name','emp_department','emp_email','emp_code','emp_contact','emp_reporting')
            ->where('emp_status', 'A')
            ->get();

        $departments = DB::table('core_employee')
            ->select('emp_department')
            ->where('emp_status', 'A')
            ->distinct()
            ->pluck('emp_department');

        return view('users.users', compact('users','roles','employees','departments'));
    }

    public function show(User $user)
    {
        $user->load(['roles']);
        $roles = Role::all();
        return view('users.show', compact('user','roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'emp_code' => 'required|string|max:50|unique:users,emp_code',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'roles'    => 'required|array|min:1',
        ]);

        $roleId = $request->roles[0];

        $employee = DB::table('core_employee')
            ->where('emp_code', $request->emp_code)
            ->first();

        if (!$employee) {
            return back()->withInput()->with('error', 'Employee not found with code: ' . $request->emp_code);
        }

        // Create reporting manager chain (with circular reference + depth protection)
        $reportingEmpId = (!empty($employee->emp_reporting) && $employee->emp_reporting != '0')
            ? (string)$employee->emp_reporting
            : null;

        if ($reportingEmpId) {
            $this->createManagerChain($reportingEmpId, $roleId, [], 0);
        }

        $user = User::create([
            'name'          => $employee->emp_name,
            'emp_code'      => $employee->emp_code,
            'mobile_number' => $employee->emp_contact,
            'emp_reporting' => $reportingEmpId,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'role_id'       => $roleId,
            'status'        => 1,
        ]);

        $user->roles()->sync([$roleId]);

        return redirect()->route('users')->with('success', 'User created successfully!');
    }

    private function createManagerChain(string $employeeId, int $roleId, array $visited, int $depth): ?User
    {
        // Stop: circular reference, too deep, or invalid
        if ($depth > 10 || in_array($employeeId, $visited) || !$employeeId || $employeeId === '0') {
            return null;
        }

        $visited[] = $employeeId;

        $employee = DB::table('core_employee')
            ->where('employee_id', $employeeId)
            ->first();

        if (!$employee) return null;

        // Already a user — return existing
        $existing = User::where('emp_code', $employee->emp_code)->first();
        if ($existing) return $existing;

        // Recursively create parent manager first
        $parentEmpId = (!empty($employee->emp_reporting) && $employee->emp_reporting != '0')
            ? (string)$employee->emp_reporting
            : null;

        if ($parentEmpId) {
            $this->createManagerChain($parentEmpId, $roleId, $visited, $depth + 1);
        }

        // Safe unique email
        $email = !empty($employee->emp_email) ? trim($employee->emp_email) : null;
        if (!$email || User::where('email', $email)->exists()) {
            $email = strtolower(preg_replace('/\s+/', '.', trim($employee->emp_name)))
                   . '.' . $employee->emp_code . '@internal.local';
        }

        $manager = User::create([
            'name'          => $employee->emp_name,
            'emp_code'      => $employee->emp_code,
            'mobile_number' => $employee->emp_contact,
            'emp_reporting' => $parentEmpId,
            'email'         => $email,
            'password'      => bcrypt($employee->emp_contact ?? 'password@123'),
            'role_id'       => $roleId,
            'status'        => 1,
        ]);

        $manager->roles()->sync([$roleId]);

        return $manager;
    }

    public function assignRole(Request $request, $id)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user = User::findOrFail($id);
        $user->role_id = $request->role_id;
        $user->save();
        $user->roles()->sync([$request->role_id]);
        return back()->with('success', 'Role updated successfully');
    }

    public function removeRole(Request $request, User $user)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user->roles()->detach($request->role_id);
        return back()->with('success', 'Role removed successfully');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);
        $user->update(['name' => $request->name, 'email' => $request->email]);
        return redirect()->route('users')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->roles()->detach();
        $user->status = 0;
        $user->save();
        return redirect()->route('users')->with('success', 'User deactivated successfully');
    }

    public function syncEmployees()
    {
        $inactiveEmployees = DB::table('core_employee')
            ->where('emp_status', 'D')
            ->pluck('emp_code');
        User::whereIn('emp_code', $inactiveEmployees)->update(['status' => 0]);
        return back()->with('success', 'Users synced successfully!');
    }
}
