@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
            <div class="items-center gap-3">
                <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                    Requests
                </h3>
                <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage germplasm requests</p>
            </div>
            <div class="d-flex gap-2 justify-content-end">
                <select style="width: 125px;" id="cropFilter" class="form-select form-select-sm">
                    <option value="">All Crops</option>
                    @foreach($crops as $crop)
                    <option value="{{ $crop->id }}">
                    {{ $crop->crop_name }}
                    </option>
                    @endforeach
                </select>

                <select  style="width: 125px;" id="userFilter" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">
                    {{ $user->name }}
                    </option>
                    @endforeach
                </select>

                <a href="{{ route('requests.create') }}" class="btn btn-sm btn-primary" >
                    <i class="ri-add-line me-1"></i>New Request
                </a>
            </div>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($requests->count())
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Request #</th>
                                <th>Request Through</th>
                                <th>Request By</th>
                                <th>Accession Number</th>
                                <th>Crop</th>
                                <th>Quantity</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Approved Date</th>
                                <th>Dispatch Date</th>
                                <th>Actions</th>
                                <th>Receiver</th>
                                <th>Return</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td><strong>{{ $request->request_number }}</strong></td>
                                <td>
                                    @if($request->request_through == '1')
                                        <span class="badge bg-info">Mail</span>
                                    @elseif($request->request_through == '2')
                                        <span class="badge bg-primary">Call</span>
                                    @else
                                        <span class="badge bg-secondary">Self</span>
                                    @endif                                  
                                    
                                </td>
                                <td>{{ optional($request->user)->name ?? '-' }}</td>
                                <td>{{ optional($request->accession)->accession_number ?? '-' }}</td>
                                <td>{{ optional($request->crop)->crop_name }}</td>
                                <td>{{ $request->quantity }} {{ optional($request->unit)->code }}</td>
                                <td>{{ $request->request_date->format('d M Y h:i A') }}</td>
                                <td>
                                    @if($request->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>

                                    @elseif($request->status === 'approved')
                                        <span class="badge bg-success">Approved</span>

                                    @elseif($request->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>

                                    @elseif($request->status === 'dispatched')
                                        <span class="badge bg-primary">Dispatched</span>

                                    @elseif($request->status === 'completed')
                                        <span class="badge bg-info">Completed</span>

                                    @elseif($request->status === 'received')
                                        <span class="badge bg-success">Received</span>

                                    @elseif($request->status === 'returned')
                                        <span class="badge bg-warning text-dark">Returned</span>

                                    @elseif($request->status === 'regenerated')
                                        <span class="badge bg-warning text-dark">Regenerated</span>

                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $request->approved_at ? $request->approved_at->format('d M Y h:i A') : '-' }}</td>
                                <td></td>
                                <td>
                                    <!-- View button always visible -->
                                    <button class="btn btn-sm btn-outline-info viewRequestBtn" 
                                        data-id="{{ $request->id }}"
                                        data-number="{{ $request->request_number }}"
                                        data-crop="{{ $request->crop->crop_name }}"
                                        data-quantity="{{ $request->quantity }}"
                                        data-unit="{{ $request->unit?->code }}"
                                        data-requester="{{ $request->requester_name }}"
                                        data-email="{{ $request->requester_email }}"
                                        data-purpose="{{ $request->purpose }}"
                                        data-status="{{ $request->status }}"
                                        data-request-date="{{ $request->request_date->format('d M Y') }}"
                                        data-required-date="{{ $request->required_date?->format('d M Y') }}"
                                        data-notes="{{ $request->notes }}">
                                        <i class="ri-eye-line"></i>
                                    </button>

                                    <!-- Only show Edit + Delete when pending -->
                                    @if($request->status === 'pending')

                                        <!-- Edit -->
                                        <a href="{{ route('requests.edit', $request->id) }}" 
                                            class="btn btn-sm btn-outline-warning">
                                            <i class="ri-edit-line"></i>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('requests.destroy', $request->id) }}" method="POST" class="d-inline d-none"
                                            onsubmit="return confirm('Delete this request?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>

                                    @endif
                                    
                                    @php
    $authUser = auth()->user();
    // Check if logged-in user is the reporting manager of the requester
    $requesterUser = $request->user;
    $isManager = false;
    if ($requesterUser && $authUser->emp_code) {
        // Find auth user's employee_id
        $authEmpId = \Illuminate\Support\Facades\DB::table('core_employee')
            ->where('emp_code', $authUser->emp_code)
            ->value('employee_id');
        $isManager = $authEmpId && (string)$requesterUser->emp_reporting === (string)$authEmpId;
    }
    $canApprove = $request->status == 'pending' && (
        $authUser->hasRole(['super-admin','admin','manager']) || $isManager
    );
@endphp

@if($canApprove)

                                    
                                        <button 
                                            class="btn btn-sm btn-success approveBtn"
                                            data-id="{{ $request->id }}"
                                            data-number="{{ $request->request_number }}"
                                        >
                                            <i class="ri-check-line"></i> Approve
                                        </button>
                                        <button 
                                            class="btn btn-sm btn-danger rejectBtn"
                                            data-id="{{ $request->id }}"
                                            data-number="{{ $request->request_number }}"
                                        >
                                            <i class="ri-delete-bin-line"></i> Reject
                                        </button>
                                    @endif
                                    @if($request->status == 'approved')
<button 
    class="btn btn-sm btn-primary invoiceBtn"
    data-id="{{ $request->id }}"
    data-number="{{ $request->request_number }}"
    data-crop="{{ $request->crop->crop_name }}"
    data-quantity="{{ $request->quantity }}"
    data-unit="{{ $request->unit?->code }}"
>
<i class="ri-file-text-line"></i> Dispatch
</button>
@endif
                                </td>
                                <td>
                                    @if($request->status === 'dispatched')
                                        <button class="btn btn-sm btn-info receiveBtn"
                                            data-id="{{ $request->id }}"
                                            data-number="{{ $request->request_number }}">
                                            <i class="ri-inbox-archive-line me-1"></i>Receive
                                        </button>
                                    @elseif($request->status === 'received')
                                        <button class="btn btn-sm btn-warning returnBtn"
                                            data-id="{{ $request->id }}"
                                            data-number="{{ $request->request_number }}"
                                            data-quantity="{{ $request->quantity }}"
                                            data-crop="{{ $request->crop?->crop_name }}"
                                            data-unit="{{ $request->unit?->name }}">
                                            <i class="ri-arrow-go-back-line me-1"></i>Return
                                        </button>
                                    @elseif($request->status === 'returned')
                                        <span class="text-warning small"><i class="ri-arrow-go-back-line me-1"></i>Returned</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->status === 'dispatched' || $request->status === 'received') 
                                    <button 
class="btn btn-sm btn-info regenerationsBtn"
data-id="{{ $request->id }}"
data-number="{{ $request->request_number }}"
data-crop="{{ $request->crop?->crop_name ?? '' }}"
data-quantity="{{ $request->quantity }}"
data-unit="{{ $request->unit?->name ?? '' }}"
>
Regeneration
</button>
                                    @else
                                    <span class="text-muted small">—</span> 
                                    @endif
</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5">
                <p class="text-slate-500">No requests found. <a href="#" class="add-request-link">Create one</a></p>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                TAT (Turnaround Time) Report
            </div>
             <div class="card-body">
                <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Request #</th>
                                <th>Request By</th>
                                <th>Request Date</th>
                                <th>Required Date</th>
                                <th>Status</th>
                                <th>No. of Days</th>

                                <th>Approved Date</th>
                                <th>Approved By</th>
                                <th>Status</th>
                                <th>No. of Days</th>

                                <th>Dispatch Date</th>
                                <th>Dispatch By</th>
                                <th>Status</th>
                                <th>No. of Days</th>

                                <th>Receiver Date</th>
                                <th>Receiver By</th>
                                <th>No. of Days</th>

                                <th>Return</th>
                                <th>Receiver By</th>
                                <th>No. of Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td>{{ $request->request_number }}</td>
                                <td>{{ $request->user->name ?? '-' }}</td>
                                <td>{{ $request->request_date->format('d M Y') }}</td>
                                <td>{{ $request->required_date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-warning">{{ ucfirst($request->status) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $request->req_to_approve_days }} Days
                                    </span>
                                </td>

                                <!-- Approval -->
                                <td>{{ $request->approved_at?->format('d M Y') ?? '-' }}</td>
                                <td>{{ $request->approvedBy->name ?? '-' }}</td>
                                <td></td>
                                <td>
                                    @if($request->approved_at)
                                        <span class="badge bg-success">
                                            {{ $request->req_to_approve_days }} Days
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            {{ now()->diffInDays($request->request_date) }} Days Pending
                                        </span>
                                    @endif
                                </td>

                                <!-- Dispatch -->
                                <td>{{ $request->dispatch_date ? \Carbon\Carbon::parse($request->dispatch_date)->format('d M Y') : '-' }}</td>

                                <td>{{ $request->dispatch->contact_person ?? '-' }}</td>
                                <td></td>
                                <td>
                                    @if($request->approve_to_dispatch_days !== null)
                                        <span class="badge bg-primary">
                                            {{ $request->approve_to_dispatch_days }} Days
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <!-- Receive -->
                                <td>{{ $request->receive_date?->format('d M Y') ?? '-' }}</td>
                                <td>{{ $request->received_by ?? '-' }}</td>
                                <td>
                                    @if($request->dispatch_to_receive_days !== null)
                                        <span class="badge bg-info">
                                            {{ $request->dispatch_to_receive_days }} Days
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <!-- Return -->
                                <td>{{ $request->return_date?->format('d M Y') ?? '-' }}</td>
                                <td>{{ $request->returned_by ?? '-' }}</td>
                                <td>
                                    @if($request->receive_to_return_days !== null)
                                        <span class="badge bg-warning">
                                            {{ $request->receive_to_return_days }} Days
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                    </table>
             </div>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove any existing modal backdrops on page load
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';

    // Handle View button click
    document.querySelectorAll('.viewRequestBtn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('view_request_number').textContent = this.getAttribute('data-number');
            document.getElementById('view_crop').textContent = this.getAttribute('data-crop');
            document.getElementById('view_quantity').textContent = this.getAttribute('data-quantity') + ' ' + this.getAttribute('data-unit');
            document.getElementById('view_requester').textContent = this.getAttribute('data-requester');
            document.getElementById('view_email').textContent = this.getAttribute('data-email') || 'N/A';
            document.getElementById('view_phone').textContent = this.getAttribute('data-phone') || 'N/A';
            document.getElementById('view_purpose').textContent = this.getAttribute('data-purpose') || 'N/A';
            document.getElementById('view_request_date').textContent = this.getAttribute('data-request-date');
            document.getElementById('view_required_date').textContent = this.getAttribute('data-required-date') || 'N/A';
            document.getElementById('view_notes').textContent = this.getAttribute('data-notes') || 'N/A';
            
            const status = this.getAttribute('data-status');
            let statusBadge = '';
            if (status === 'pending') statusBadge = '<span class="badge bg-warning">Pending</span>';
            else if (status === 'approved') statusBadge = '<span class="badge bg-success">Approved</span>';
            else if (status === 'rejected') statusBadge = '<span class="badge bg-danger">Rejected</span>';
            else statusBadge = '<span class="badge bg-info">Completed</span>';
            document.getElementById('view_status').innerHTML = statusBadge;

            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');

            const modalEl = document.getElementById('viewRequestModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        });
    });

    document.addEventListener('click', function(e){

    const btn = e.target.closest('.editRequestBtn');
    if(!btn) return;

    e.preventDefault();

    const id = btn.dataset.id;

    const form = document.getElementById('requestForm');

    // Fill form fields
    document.getElementById('crop_id').value = btn.dataset.cropId;
    document.getElementById('quantity').value = btn.dataset.quantity;
    document.getElementById('unit_id').value = btn.dataset.unitId;
    document.getElementById('requester_name').value = btn.dataset.requester;
    document.getElementById('requester_email').value = btn.dataset.email || '';
    document.getElementById('purpose').value = btn.dataset.purpose || '';
    document.getElementById('request_date').value = btn.dataset.requestDate;
    document.getElementById('required_date').value = btn.dataset.requiredDate || '';
    document.getElementById('notes').value = btn.dataset.notes || '';
    document.getElementById('status').value = btn.dataset.status;

    document.getElementById('statusRow').style.display = 'block';

    // remove existing method
    const old = form.querySelector('input[name="_method"]');
    if(old) old.remove();

    // add PUT
    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'PUT';
    form.appendChild(method);

    // update route
    form.action = "/requests/" + id;

    console.log("Edit ID:", id);
    console.log("Form Action:", form.action);

    const modal = new bootstrap.Modal(document.getElementById('requestModal'));
    modal.show();

});

    // Handle Add button click
    const addBtn = document.getElementById('addRequestBtn');
    if (addBtn) {
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('requestForm').reset();
            document.getElementById('requestForm').action = "{{ route('requests.store') }}";
            document.getElementById('requestModalLabel').textContent = 'Add Request';
            document.getElementById('submitBtn').textContent = 'Add Request';
            document.getElementById('statusRow').style.display = 'none';
            document.getElementById('request_date').value = "{{ date('Y-m-d') }}";
            const existing = document.getElementById('requestForm').querySelector('input[name="_method"]');
            if (existing) existing.remove();
            
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            
            const modalEl = document.getElementById('requestModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        });
    }

    // Handle "Create one" link
    const addLink = document.querySelector('.add-request-link');
    if (addLink) {
        addLink.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('addRequestBtn').click();
        });
    }

    // Clean up backdrops when modals are hidden
    const requestModalEl = document.getElementById('requestModal');
    if (requestModalEl) {
        requestModalEl.addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }

    const viewModalEl = document.getElementById('viewRequestModal');
    if (viewModalEl) {
        viewModalEl.addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }

    document.querySelectorAll('.receiveBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('receiveRequestNumber').textContent = this.dataset.number;
            document.getElementById('receiveForm').action = '/requests/' + this.dataset.id + '/receive';
            new bootstrap.Modal(document.getElementById('receiveModal')).show();
        });
    });

    document.querySelectorAll('.returnBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('returnRequestNumber').textContent = d.number;
            document.getElementById('ret_crop').textContent    = d.crop    || '—';
            document.getElementById('ret_qty').textContent     = d.quantity + ' ' + (d.unit || '');
            document.getElementById('returnQtyInput').max      = d.quantity;
            document.getElementById('returnForm').action = '/requests/' + d.id + '/return';
            new bootstrap.Modal(document.getElementById('returnModal')).show();
        });
    });

    document.querySelectorAll('.regenerationsBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const g = this.dataset;
        document.getElementById('regenRequestNumber').textContent = g.number || '—';
        document.getElementById('retg_crop').textContent = g.crop || '—';
        document.getElementById('retg_qty').textContent = (g.quantity || '0') + ' ' + (g.unit || '');

        document.getElementById('regenerationForm').action = `/requests/${g.id}/return`;

        new bootstrap.Modal(document.getElementById('regenerationsModal')).show();
        });
    });

    document.querySelectorAll('input[name="return_type"]').forEach(radio => {
        radio.addEventListener('change', function () {
            let partialFields = document.getElementById('partialReturnFields');
            if (this.value === 'partial') {
                partialFields.style.display = 'flex';
            } else {
                partialFields.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.approveBtn').forEach(btn => {

        btn.addEventListener('click', function() {

            let id = this.dataset.id;
            let number = this.dataset.number;

            document.getElementById('approveRequestNumber').innerText = number;

            let form = document.getElementById('approveForm');
            form.action = "/requests/" + id + "/approve";

            let modal = new bootstrap.Modal(document.getElementById('approveModal'));
            modal.show();

        });

    });

    document.querySelectorAll('.rejectBtn').forEach(btn => {

        btn.addEventListener('click', function() {

            let id = this.dataset.id;
            let number = this.dataset.number;

            document.getElementById('rejectRequestNumber').innerText = number;

            let form = document.getElementById('rejectForm');
            form.action = "/requests/" + id + "/reject";

            let modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();

        });

    });

    document.querySelectorAll('.invoiceBtn').forEach(btn => {

    btn.addEventListener('click', function(){

    document.getElementById('inv_request_no').innerText = this.dataset.number;
    document.getElementById('inv_crop').innerText = this.dataset.crop;
    document.getElementById('inv_quantity').innerText = this.dataset.quantity + " " + this.dataset.unit;

    let modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
    modal.show();

    });

    });

    function printInvoice() {

    let printContents = document.getElementById('invoiceContent').innerHTML;
    let originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
    location.reload();

    }

    document.getElementById('cropFilter').addEventListener('change', filterRequests);
document.getElementById('userFilter').addEventListener('change', filterRequests);

function filterRequests() {

    let crop = document.getElementById('cropFilter').value;
    let user = document.getElementById('userFilter').value;

    let url = new URL(window.location.href);

    if(crop){
        url.searchParams.set('crop', crop);
    }else{
        url.searchParams.delete('crop');
    }

    if(user){
        url.searchParams.set('user', user);
    }else{
        url.searchParams.delete('user');
    }

    window.location.href = url.toString();
}

});
</script>
@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function () {

    const modal = bootstrap.Modal.getInstance(document.getElementById('requestModal'));
    if(modal){
        modal.hide();
    }

    setTimeout(function(){
        window.location.reload();
    }, 300);

});
</script>
@endif
@endsection
@section('modals')

<div class="modal fade" id="invoiceModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Request Dispatch</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body" id="invoiceContent">

<h5>Germplasm Request Dispatch List</h5>
<hr>

<p><strong>Request No:</strong> <span id="inv_request_no"></span></p>
<p><strong>Crop:</strong> <span id="inv_crop"></span></p>
<p><strong>Quantity:</strong> <span id="inv_quantity"></span></p>

<hr>

<p><strong>Date:</strong> {{ date('d M Y') }}</p>

</div>

<div class="modal-footer">
<button class="btn btn-light" data-bs-dismiss="modal">Close</button>
</div>

</div>
</div>
</div>

<div class="modal fade" id="approveModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Approve Request</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="approveForm">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    <p>Approve Request: <strong id="approveRequestNumber"></strong></p>

                    <div class="mb-3">
                        <label class="form-label">Remarks <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control" required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Reject Request</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="rejectForm">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    <p>Reject Request: <strong id="rejectRequestNumber"></strong></p>

                    <div class="mb-3">
                        <label class="form-label">Remarks <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control" required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Rejected</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Request Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestModalLabel">Edit Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="requestForm" method="POST" action="{{ route('requests.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="crop_id" class="form-label">Crop <span class="text-danger">*</span></label>
                            <select name="crop_id" id="crop_id" class="form-select" required>
                                <option value="">Select Crop</option>
                                @foreach($crops as $crop)
                                    <option value="{{ $crop->id }}">{{ $crop->crop_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="quantity" id="quantity" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                            <select name="unit_id" id="unit_id" class="form-select" required>
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="requester_name" class="form-label">Requester Name <span class="text-danger">*</span></label>
                            <input type="text" name="requester_name" id="requester_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="requester_email" class="form-label">Requester Email</label>
                            <input type="email" name="requester_email" id="requester_email" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="request_date" class="form-label">Request Date <span class="text-danger">*</span></label>
                            <input type="date" name="request_date" id="request_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="required_date" class="form-label">Required Date</label>
                            <input type="date" name="required_date" id="required_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="purpose" class="form-label">Purpose</label>
                            <input type="text" name="purpose" id="purpose" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div id="statusRow" class="col-md-6 mb-3" style="display: none;">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" style="width: auto;height:auto;position: relative;top: auto;margin: auto;left: 0;">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
              
                        <div class="col-md-6 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Request Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1" role="dialog" aria-labelledby="viewRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRequestModalLabel">Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-md-6 card mb-0">
                <div class="card-header bg-light p-2"><strong>Information</strong></div>
                <div class="card-body p-1">
                    
                    <table class="table table-bordered table-striped p-0">
            <tbody>

                <!-- BASIC -->
                <tr><th style="width: 30%;">Request Number:</th><td><p class="mb-0" id="view_request_number"></p></td></tr>
                <tr><th style="width: 30%;">Status</th><td><p class="mb-0" id="view_status"></p></td></tr>
                <tr><th>Crop</th><td><p class="mb-0" id="view_crop"></p></td></tr>
                <tr><th>Quantity</th><td><p class="mb-0" id="view_quantity"></p></td></tr>
                <tr><th>Required Date</th><td><p class="mb-0" id="view_required_date"></p></td></tr>
            </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6 card mb-0">
                <div class="card-header bg-light p-2"><strong> Requester Information</strong></div>
                <div class="card-body p-1">
                    
                    <table class="table table-bordered table-striped p-0">
            <tbody>

                <!-- BASIC -->
                <tr><th style="width: 30%;">Requester</th><td><p class="mb-0" id="view_requester"></p></td></tr>
                <tr><th>Email</th><td><p class="mb-0" id="view_email"></p></td></tr>
                <tr><th>Phone Number</th><td><p class="mb-0" id="view_phone"></p></td></tr>
                <tr><th>Purpose</th><td><p class="mb-0" id="view_purpose"></p></td></tr>
                <tr><th>Notes</th><td><p class="mb-0" id="view_notes"></p></td></tr>                
                <tr><th>Request Date:</th><td><p class="mb-0" id="view_request_date"></p></td></tr>
            </tbody>
                    </table>
                </div>
            </div>
        </div>

                
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Return Modal --}}
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-arrow-go-back-line me-2"></i>Return Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="returnForm" action="#">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Return for request <strong id="returnRequestNumber"></strong></p>
                    <div class="card bg-light mb-3">
                        <div class="card-body py-2 small">
                            <div class="row g-1">
                                <div class="col-6"><span class="text-muted">Crop:</span> <span id="ret_crop"></span></div>
                                <div class="col-6"><span class="text-muted">Dispatched Qty:</span> <span id="ret_qty"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Return Date</label>
                        <input type="date" name="return_date" class="form-control"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Return Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="return_quantity" id="returnQtyInput"
                               class="form-control" step="0.01" min="0.01" required
                               placeholder="Quantity being returned">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Return Remarks</label>
                        <textarea name="return_remarks" class="form-control" rows="3"
                                  placeholder="Reason for return, condition of seeds, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ri-arrow-go-back-line me-1"></i> Confirm Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Receive Modal --}}
<div class="modal fade" id="receiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-inbox-archive-line me-2"></i>Confirm Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="receiveForm" action="#">
                @csrf
                <div class="modal-body">
                    <p>Mark request <strong id="receiveRequestNumber"></strong> as <span class="badge bg-success">Received</span>?</p>
                    
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Receive Date</label>
                            <input type="date" name="receive_date" class="form-control"
                                value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Receiving Condition <span class="text-danger">*</span></label>
                            <select name="receiving_condition" class="form-select" required>
                                <option value="">Select Condition</option>
                                <option value="good">Good</option>
                                <option value="damaged">Damaged</option>
                                <option value="partial">Partially Damaged</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Upload Photo</label>
                            <input class="form-control" name="receiving_file" type="file" >
                            <small class="text-muted">Max 5 images (JPG, PNG, GIF — max 2MB each)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Receive Note / Remarks</label>
                        <textarea name="receive_remarks" class="form-control" rows="3"
                                  placeholder="e.g. Package received in good condition..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-check-double-line me-1"></i> Confirm Received
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Regeneration Modal --}}
<div class="modal fade" id="regenerationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-inbox-archive-line me-2"></i>Confirm Regeneration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="regenerationForm">
                @csrf
                <div class="modal-body">                  
                    <p>Mark request <strong id="regenRequestNumber"></strong> as needing regeneration?</p>
                    <div class="card bg-light mb-3">
                        <div class="card-body py-2 small">
                            <div class="row g-1">
                                <div class="col-6"><span class="text-muted">Crop:</span> <span id="retg_crop"></span></div>
                                <div class="col-6"><span class="text-muted">Dispatched Qty:</span> <span id="retg_qty"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="partialReturnFields">
                        <input type="hidden" name="return_type" value="regeneration">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Germination Rate</label>
                            <input type="number" name="germination_rate" class="form-control" placeholder="Enter germination rate">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Moisture Rate</label>
                            <input type="number" name="moisture_rate" class="form-control" placeholder="Enter moisture rate">
                        </div>
                    </div>

                    <div class="row mt-2" >
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Re generation Return Date</label>
                            <input type="date" name="return_date" class="form-control"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="return_quantity" class="form-control" placeholder="Enter quantity">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Regeneration Note / Remarks</label>
                        <textarea name="return_remarks" class="form-control" rows="3"
                                  placeholder="e.g. Package returned in good condition..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-check-double-line me-1"></i> Confirm Regenerated
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection