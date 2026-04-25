@extends('layouts.app')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       {{ isset($seedRequest) ? 'Edit Request' : 'New Request' }}
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Create a new request for crops or varieties</p>
                </div>
                <a href="{{ route('requests.index') }}" class="btn btn-sm btn-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="card col-lg-8 col-md-12">
        <div class="card-body ">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <form action="{{ isset($seedRequest) && $seedRequest->id ? route('requests.update', $seedRequest->id) : route('requests.store') }}" method="POST">
    @csrf

    @if(isset($seedRequest) && $seedRequest->id)
        @method('PUT')
    @endif
                @php
$role = optional(auth()->user()->role)->slug;
@endphp
                <div class="row">
                    @if(isset($seedRequest) && $seedRequest->exists)
                    <div class="col-12 mb-2">
                        <small class="text-muted">Editing: <strong>{{ $seedRequest->request_number }}</strong></small>
                    </div>
                    @endif
                    @if(in_array($role, ['super-admin','admin']))
                    <div class="col-md-4 mb-3">
                        <label for="request_through" class="form-label">
                            Request Through <span class="text-danger">*</span>
                        </label>

                        <select name="request_through" id="request_through" 
                            class="form-select @error('request_through') is-invalid @enderror" required>

                            <option value="">Select Option</option>

                            <option value="1" {{ old('request_through', $seedRequest->request_through ?? '') == '1' ? 'selected' : '' }}>
                                Mail
                            </option>

                            <option value="2" {{ old('request_through', $seedRequest->request_through ?? '') == '2' ? 'selected' : '' }}>
                                Call
                            </option>
                            <option value="3" {{ old('request_through', $seedRequest->request_through ?? '') == '3' ? 'selected' : '' }}>
                                Self
                            </option>
                            <option value="4" {{ old('request_through', $seedRequest->request_through ?? '') == '4' ? 'selected' : '' }}>
                                Whatsapp
                            </option>

                        </select>

                        @error('request_through')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Select User <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" id="userSelect" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    data-emp-reporting="{{ $user->emp_reporting }}"
                                    {{ old('user_id', $seedRequest->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label for="request_date" class="form-label">Request Date <span class="text-danger">*</span></label>
                        <input type="date" name="request_date" id="request_date" 
                                class="form-control @error('request_date') is-invalid @enderror" 
                                value="{{ old('request_date', isset($seedRequest) && $seedRequest->request_date ? $seedRequest->request_date->format('Y-m-d') : date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                        @error('request_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="co-md-12">
                        {{-- Reporting Manager Details --}}
                        <div id="userReportingCard" class="card border bg-light d-none">
                            <div class="card-header py-2 bg-white d-flex align-items-center gap-2">
                                <i class="ri-user-star-line text-primary"></i>
                                <strong class="small">Reporting Manager</strong>
                                <span id="rm_user_badge_req" class="ms-auto"></span>
                            </div>
                            <div class="card-body py-2 small">
                                <div class="row g-1">
                                    <div class="col-4"><span class="text-muted">Name:</span> <span id="req_rm_name">—</span></div>
                                    <div class="col-4"><span class="text-muted">Email:</span> <span id="req_rm_email">—</span></div>
                                    <div class="col-4"><span class="text-muted">Mobile:</span> <span id="req_rm_mobile">—</span></div>
                                    <div class="col-4"><span class="text-muted">Code:</span> <span id="req_rm_code">—</span></div>
                                    <div class="col-4"><span class="text-muted">Dept:</span> <span id="req_rm_dept">—</span></div>
                                    <div class="col-4"><span class="text-muted">Designation:</span> <span id="req_rm_desig">—</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @else
                    <input type="hidden" name="request_through" value="3">
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="request_date" value="{{ date('Y-m-d') }}">
                    @endif


                    <div class="col-md-4 mb-3">
                        <label for="selectpurpose" class="form-label">Select Purpose <span class="text-danger">*</span></label>
                        <select name="purpose" id="selectpurpose" class="form-select @error('purpose') is-invalid @enderror" required>
                            <option value="">Select Purpose</option>
                            <option value="For Research" 
                            {{ old('purpose',$seedRequest->purpose ?? '') == 'For Research' ? 'selected' : '' }}>
                            For Research
                            </option>
                            <option value="For Academic Use"
                            {{ old('purpose',$seedRequest->purpose ?? '') == 'For Academic Use' ? 'selected' : '' }}>
                            For Academic Use
                            </option>
                            
                            <option value="For Breeding Program"
                             {{ old('purpose',$seedRequest->purpose ?? '') == 'For Breeding Program' ? 'selected' : '' }}>
                             For Breeding Program</option>
                            <option value="For Institutional Collaboration" {{ old('purpose',$seedRequest->purpose ?? '') == 'For Institutional Collaboration' ? 'selected' : '' }}>For Institutional Collaboration</option>
                            <option value="For External Research Partner" {{ old('purpose',$seedRequest->purpose ?? '') == 'For External Research Partner' ? 'selected' : '' }}>For External Research Partner</option>
                        </select>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                </div>
                <div class="row" id="requestRows">
                    <div class="col-md-3">
            <label class="form-label">Crop <span class="text-danger">*</span></label>
        </div>
        <div class="col-md-3">
            <label class="form-label">Accession Number <span class="text-danger">*</span></label>
        </div>
        <div class="col-md-2">
            <label class="form-label">Available Quantity <span class="text-danger">*</span></label>
        </div>
        <div class="col-md-1">
            <label class="form-label">Unit <span class="text-danger">*</span></label>
        </div>
        <div class="col-md-2">
            <label class="form-label">Request Quantity <span class="text-danger">*</span></label>
        </div>
    <div class="row request-row mb-2">
        
        <div class="col-md-3">
            <select name="crop_id[]" class="form-select cropSelect" required>
                <option value="">Select Crop</option>
                @foreach($crops as $crop)
                    <option value="{{ $crop->id }}">{{ $crop->crop_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="accession_id[]" class="form-select accessionSelect" required>
                <option value="">Select Accession</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="number" step="0.01" class="form-control availableQty" readonly>
        </div>
        <div class="col-md-1">
            <span class="input-group-text availableUnit">--</span>

            <input type="hidden" name="unit_id[]" class="unit_id">
        </div>

        <div class="col-md-2">
            <input type="number" step="0.01" name="quantity[]" class="form-control" required>
        </div>

        <div class="col-md-1">
            <button type="button" class="btn btn-danger removeRow">X</button>
        </div>
    </div>
</div>

<button type="button" id="addRow" class="btn btn-sm btn-primary">+ Add More</button>

                {{-- Accession Details Box --}}
                <div id="accessionDetailsBox" class="card border bg-light mb-3 d-none">
                    <div class="card-header py-2 bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-semibold small"><i class="ri-information-line me-1"></i>Accession Details</span>
                        <div class="d-flex align-items-center gap-2">
                            <span id="acc_number" class="badge bg-primary"></span>
                            <a href="#" id="viewDetailsLink" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="display:none">
                                <i class="ri-external-link-line me-1"></i>View Details
                            </a>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 small">
                            <div class="col-md-3"><span class="text-muted">Accession Name:</span><br><strong id="acc_name">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Crop:</span><br><strong id="acc_crop">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Scientific Name:</span><br><strong id="acc_scientific">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Total Quantity:</span><br><strong id="acc_total_qty" class="text-info">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Available Qty (User):</span><br><strong id="acc_qty" class="text-success">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Unit:</span><br><strong id="acc_unit">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Expiry Date:</span><br><strong id="acc_expiry">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Barcode:</span><br><strong id="acc_barcode">—</strong></div>
                        </div>
                        {{-- Lots breakdown --}}
                        <div id="accLotsWrapper" class="mt-2 d-none">
                            <div class="fw-semibold small mb-1"><i class="ri-stack-line me-1"></i>Lots Breakdown</div>
                            <table class="table table-sm table-bordered mb-0" style="font-size:12px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Lot No.</th>
                                        <th>Storage</th>
                                        <th class="text-end">Available Qty</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody id="accLotsBody"></tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2" class="text-end">Total</th>
                                        <th class="text-end" id="accLotsTotal">—</th>
                                        <th id="accLotsTotalUnit">—</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="required_date" class="form-label">Required Date</label>
                        <input type="date" name="required_date" id="required_date" 
                               class="form-control @error('required_date') is-invalid @enderror" 
                              value="{{ old('required_date', isset($seedRequest) && $seedRequest->required_date ? $seedRequest->required_date->format('Y-m-d') : '') }}" min="">
                        @error('required_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="purpose_details" class="form-label">Purpose Details</label>
                        <input type="text" name="purpose_details" value="{{ old('purpose_details',$seedRequest->purpose_details ?? '') }}" id="purpose_details" class="form-control">
                        @error('purpose_details')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes',$seedRequest->notes ?? '') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('requests.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ isset($seedRequest) ? 'Update Request' : 'Create Request' }}</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // 👤 Reporting Manager Fetch
    // =========================
    const userSelect = document.getElementById('userSelect');

    if (userSelect) {
        userSelect.addEventListener('change', function () {
            const card = document.getElementById('userReportingCard');
            const selected = this.options[this.selectedIndex];
            const reportingEmpId = selected.getAttribute('data-emp-reporting');

            if (!this.value || !reportingEmpId || reportingEmpId === '0' || reportingEmpId === '') {
                card.classList.add('d-none');
                return;
            }

            fetch(`/employee/${reportingEmpId}`)
                .then(r => r.json())
                .then(m => {
                    if (!m) { card.classList.add('d-none'); return; }
                    document.getElementById('req_rm_name').textContent   = m.emp_name        || '—';
                    document.getElementById('req_rm_code').textContent   = m.emp_code        || '—';
                    document.getElementById('req_rm_email').textContent  = m.emp_email       || '—';
                    document.getElementById('req_rm_mobile').textContent = m.emp_contact     || '—';
                    document.getElementById('req_rm_dept').textContent   = m.emp_department  || '—';
                    document.getElementById('req_rm_desig').textContent  = m.emp_designation || '—';
                    // Check if manager is a user
                    fetch(`/check-user?emp_code=${m.emp_code}`)
                        .then(r => r.json())
                        .then(u => {
                            document.getElementById('rm_user_badge_req').innerHTML = u.exists
                                ? '<span class="badge bg-success"><i class="ri-check-line me-1"></i>User</span>'
                                : '<span class="badge bg-secondary">Not a User</span>';
                        });
                    card.classList.remove('d-none');
                })
                .catch(() => card.classList.add('d-none'));
        });
    }

    // =========================
    // ➕ Add New Row
    // =========================
    document.getElementById('addRow').addEventListener('click', function () {
        let row = document.querySelector('.request-row').cloneNode(true);

        row.querySelectorAll('input').forEach(i => {
            i.value = '';
            i.classList.remove('is-invalid');
        });

        row.querySelectorAll('select').forEach(s => s.selectedIndex = 0);

        document.getElementById('requestRows').appendChild(row);
    });

    // =========================
    // ❌ Remove Row
    // =========================
    document.addEventListener('click', function(e){
        if(e.target.classList.contains('removeRow')){
            let rows = document.querySelectorAll('.request-row');
            if(rows.length > 1){
                e.target.closest('.request-row').remove();
            }
        }
    });

    // =========================
    // 🔄 Crop → Load Accessions
    // =========================
    document.addEventListener('change', function(e){

        if(e.target.classList.contains('cropSelect')){
            let cropId = e.target.value;
            let row = e.target.closest('.request-row');
            let accessionSelect = row.querySelector('.accessionSelect');

            accessionSelect.innerHTML = '<option>Loading...</option>';

            if(!cropId){
                accessionSelect.innerHTML = '<option value="">Select Accession</option>';
                return;
            }

            fetch(`/get-accessions/${cropId}`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">Select Accession</option>';
                    data.forEach(acc => {
                        options += `<option value="${acc.id}">${acc.accession_number}</option>`;
                    });
                    accessionSelect.innerHTML = options;
                });
        }

    });

    // =========================
    // 🔄 Accession → Load Details
    // =========================
    document.addEventListener('change', function (e) {

    if (e.target.classList.contains('accessionSelect')) {

        let id = e.target.value;
        let row = e.target.closest('.request-row');

        if (!id || !row) return;

        fetch(`/accession/${id}`)
            .then(res => {
                if (!res.ok) throw new Error('Invalid response');
                return res.json();
            })
            .then(d => {

                // =========================
                // ✅ ROW BASED VALUES
                // =========================

                // Quantity — use total across all lots
                let qtyInput = row.querySelector('.availableQty');
                if (qtyInput) {
                    qtyInput.value = d.quantity_show ?? d.quantity ?? 0;
                }

                // Unit (row-based FIX)
                let unitText = row.querySelector('.availableUnit');
                if (unitText) {
                    unitText.textContent = d.unit || '--';
                }

                // Hidden unit_id (VERY IMPORTANT 🔥)
                let unitIdInput = row.querySelector('.unit_id');
                if (unitIdInput) {
                    unitIdInput.value = d.unit_id || '';
                }

                // Max validation — total available across all lots
                let requestQty = row.querySelector('input[name="quantity[]"]');
                if (requestQty) {
                    requestQty.setAttribute('max', d.quantity_show ?? d.quantity ?? 0);
                }

                // =========================
                // ✅ GLOBAL DETAILS BOX
                // =========================

                document.getElementById('acc_name').textContent = d.accession_name || '—';
                document.getElementById('acc_crop').textContent = d.crop || '—';
                document.getElementById('acc_scientific').textContent = d.scientific_name || '—';
                document.getElementById('acc_total_qty').textContent =
                    d.total_quantity ? d.total_quantity + ' ' + (d.unit || '') : '—';
                document.getElementById('acc_qty').textContent =
                    (d.quantity_show ?? d.quantity) ? (d.quantity_show ?? d.quantity) + ' ' + (d.unit || '') : '—';
                document.getElementById('acc_unit').textContent = d.unit || '—';
                document.getElementById('acc_expiry').textContent = d.expiry_date || '—';
                document.getElementById('acc_barcode').textContent = d.barcode || '—';

                document.getElementById('acc_expiry').textContent = d.expiry_date || '—';
                document.getElementById('acc_barcode').textContent = d.barcode || '—';

                document.getElementById('accessionDetailsBox').classList.remove('d-none');

                // =========================
                // ✅ LOTS BREAKDOWN TABLE
                // =========================
                const lotsWrapper = document.getElementById('accLotsWrapper');
                const lotsBody    = document.getElementById('accLotsBody');
                const lotsTotal   = document.getElementById('accLotsTotal');
                const lotsTotalUnit = document.getElementById('accLotsTotalUnit');

                if (d.lots && d.lots.length > 0) {
                    lotsBody.innerHTML = d.lots.map(lot => `
                        <tr>
                            <td>${lot.lot_number}</td>
                            <td>${lot.storage}</td>
                            <td class="text-end">${lot.quantity}</td>
                            <td>${lot.unit}</td>
                        </tr>
                    `).join('');
                    lotsTotal.textContent   = d.quantity_show ?? d.quantity ?? 0;
                    lotsTotalUnit.textContent = d.unit || '';
                    lotsWrapper.classList.remove('d-none');
                } else {
                    lotsWrapper.classList.add('d-none');
                }

            })
            .catch(err => {
                console.error(err);
            });
    }

});
    

    // =========================
    // ⚠️ Quantity Validation
    // =========================
    document.addEventListener('input', function(e){

        if(e.target.name === "quantity[]"){
            let max = parseFloat(e.target.getAttribute('max'));
            let val = parseFloat(e.target.value);

            if (!isNaN(max) && !isNaN(val) && val > max) {
                e.target.classList.add('is-invalid');
            } else {
                e.target.classList.remove('is-invalid');
            }
        }

    });

    // =========================
    // 🚫 Prevent Submit if Invalid
    // =========================
    document.querySelector('form').addEventListener('submit', function(e){

        let invalid = false;

        document.querySelectorAll('input[name="quantity[]"]').forEach(input => {
            let max = parseFloat(input.getAttribute('max'));
            let val = parseFloat(input.value);

            if (!isNaN(max) && !isNaN(val) && val > max) {
                input.classList.add('is-invalid');
                invalid = true;
            }
        });

        if(invalid){
            e.preventDefault();
            alert('One or more rows have a request quantity that exceeds the total available stock. Please check the highlighted fields.');
        }

    });

});
</script>

@endpush
