@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    
    <!-- Success Message -->
    
    <div class="col-12">
         <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                        Crop Request List
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage crop
                        master data</p>
                </div>
            <div class="d-flex gap-2">
                <a href="{{ route('crops.index') }}" class="btn btn-sm btn-secondary ">Back to List</a>
            </div>
        </div>

   @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif 


    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Crop Name</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cropRequests as $key => $request)
                            <tr>
                                <td>{{ $key + 1 }}</td>

                                <td class="fw-semibold">
                                    {{ $request->crop_name }}
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $request->crop_code }}
                                    </span>
                                </td>

                                <td class="text-muted small">
                                    {{ $request->description ?? '-' }}
                                </td>

                                <td>
                                    {{ $request->user->name ?? 'N/A' }}
                                </td>

                                <!-- Status -->
                                <td>
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($request->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($request->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $request->created_at->format('d M Y') }}
                                </td>

                                <!-- Actions -->
                               
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No crop requests found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $cropRequests->links() }}
            </div>

        </div>
    </div>

</div>
@endsection