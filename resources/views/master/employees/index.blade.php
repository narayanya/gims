@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Employee Master</h3>
                <p class="text-muted mb-0" style="font-size:13px"></p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Emp ID</th>
                                <th>Code</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($employees as $i => $emp)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $emp->emp_name }}</td>
                                <td>{{ $emp->emp_department }}</td>
                                <td>{{ $emp->employee_id }}</td>
                                <td>{{ $emp->emp_code }}</td>
                                <td>{{ $emp->emp_email ?? '-' }}</td>
                                <td>
                                    @if($emp->emp_status == 'A')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No Employees Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="mt-3">
                    {{ $employees->links() }}
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

