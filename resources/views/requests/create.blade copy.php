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
                                value="{{ old('request_date', isset($seedRequest) && $seedRequest->request_date ? $seedRequest->request_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        @error('request_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="co-md-12">
                        <div id="ReportingDetailsBox" class="card border bg-light mb-3 ">
                            <div class="card-header py-2 bg-white d-flex justify-content-between align-items-center">
                                <span class="fw-semibold small"><i class="ri-information-line me-1"></i>Reporting Details</span>
                            </div>
                            <div class="card-body py-2">
                                <div class="row g-2 small">
                                    <div class="col-md-3">
                                        <span class="text-muted">Reporting Name:</span><br>
                                        <strong id="rep_name">—</strong>
                                    </div>

                                    <div class="col-md-3">
                                        <span class="text-muted">Reporting Email:</span><br>
                                        <strong id="rep_email">—</strong>
                                    </div>

                                    <div class="col-md-3">
                                        <span class="text-muted">Department:</span><br>
                                        <strong id="rep_department">—</strong>
                                    </div>
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
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="crop_id" class="form-label">Crop <span class="text-danger">*</span></label>
                        <select name="crop_id" id="crop_id" class="form-select @error('crop_id') is-invalid @enderror" required>
                            <option value="">Select Crop</option>
                            @foreach($crops as $crop)
                                <option value="{{ $crop->id }}"
{{ old('crop_id', $seedRequest->crop_id ?? '') == $crop->id ? 'selected' : '' }}>
                                    {{ $crop->crop_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('crop_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="Accession ID" class="form-label">Accession Number/Name <span class="text-danger">*</span></label>
                        <select name="accession_id" id="accession_id" class="form-select" required>
                            <option value="">Select Accession</option>
                        </select>
                        @error('accession_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Available Quantity</label>
                        <div class="input-group">
                            <input type="number" step="0.01" id="availableQty"
                                class="form-control bg-light" readonly>  
                            <span class="input-group-text" id="availableUnit">--</span>
                            <input type="hidden" name="unit_id" id="unit_id">
                        </div>
                        <small class="text-muted">Auto-filled from accession</small>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="quantity" class="form-label">Request Quantity <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="quantity" id="quantity" 
                               class="form-control @error('quantity') is-invalid @enderror" 
                               value="{{ old('quantity',$seedRequest->quantity ?? '') }}" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

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
                            <div class="col-md-3"><span class="text-muted">Available Qty:</span><br><strong id="acc_qty">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Warehouse:</span><br><strong id="acc_warehouse">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Status:</span><br><strong id="acc_status">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Expiry Date:</span><br><strong id="acc_expiry">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Biological Status:</span><br><strong id="acc_bio">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Sample Type:</span><br><strong id="acc_sample">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Collection Site:</span><br><strong id="acc_site">—</strong></div>
                            <div class="col-md-3"><span class="text-muted">Barcode:</span><br><strong id="acc_barcode">—</strong></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    
                    

                    {{--<div class="col-md-4 mb-3 d-none">
                        <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                        
                        <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror" readonly>
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
{{ old('unit_id',$seedRequest->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>--}}
                    
                </div>

                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="required_date" class="form-label">Required Date</label>
                        <input type="date" name="required_date" id="required_date" 
                               class="form-control @error('required_date') is-invalid @enderror" 
                              value="{{ old('required_date', isset($seedRequest) && $seedRequest->required_date ? $seedRequest->required_date->format('Y-m-d') : '') }}">
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
    const preselectedAccessionId = "{{ $preselectedAccessionId ?? '' }}";
</script>
<script>

    document.getElementById('userSelect').addEventListener('change', function () {
        let id = this.value;

        if (!id) return;

        fetch(`/employee/${id}`)
            .then(res => res.json())
            .then(d => {

                // Employee info (optional)
                document.getElementById('rep_department').textContent = d.emp_department || '—';

                // 🔥 Reporting Manager
                document.getElementById('rep_name').textContent = d.reporting_name || '—';
                document.getElementById('rep_email').textContent = d.reporting_email || '—';

            });
    });


$(document).ready(function(){

    const selectedAccessionId = '{{ old('accession_id', isset($seedRequest) ? $seedRequest->accession_id : '') }}';

    // Crop → Accession
    $('#crop_id').on('change', function(){
        let crop_id = $(this).val();

        $('#accession_id').html('<option value="">Select Accession</option>');

        if(!crop_id){
            $('#accession_id').html('<option value="">Select Accession</option>');
            return;
        }

        $.get('/get-accessions/' + crop_id, function(data){
            let options = '<option value="">Select Accession</option>';
            data.forEach(function(a){
                let sel = (selectedAccessionId == a.id) ? 'selected' : '';
                options += `<option value="${a.id}" ${sel}>${a.accession_number}</option>`;
            });
            $('#accession_id').html(options);

            // ✅ TRIGGER EVENT
            $(document).trigger('accessionsLoaded');

            if(selectedAccessionId){
                $('#accession_id').trigger('change');
            }
        });
    });


    // Accession → Details Box
    $('#accession_id').on('change', function () {
        const id = $(this).val();
        const box = $('#accessionDetailsBox');

        // clear auto-filled fields
        document.getElementById('expiryDate') && (document.getElementById('expiryDate').value = '');
        document.getElementById('germinationPct') && (document.getElementById('germinationPct').value = '');
        document.getElementById('moistureContent') && (document.getElementById('moistureContent').value = '');
        document.getElementById('purityPct') && (document.getElementById('purityPct').value = '');

        $('#availableQty').val('');
        $('#availableUnit').text('--'); // ✅ reset unit
        $('#viewDetailsLink').hide();

        if (!id) {
            box.addClass('d-none');
            return;
        }

        fetch(`/lot-management/accession/${id}`)
            .then(r => r.json())
            .then(d => {

                $('#acc_number').text(d.accession_number || '');
                $('#viewDetailsLink').attr('href', '/accessions/' + d.id).show();
                $('#acc_name').text(d.accession_name || '—');
                $('#acc_crop').text(d.crop || '—');
                $('#acc_scientific').text(d.scientific_name || '—');

                // ✅ Quantity + Unit handling (FIXED)
                const qty = d.quantity ?? '';
                const unit = d.unit || '--';

                // Show in details section
                $('#acc_qty').text(qty !== '' ? qty + ' ' + unit : '—');

                // Show in input (number only)
                $('#availableQty').val(qty !== '' ? qty : '');

                // Show unit separately
                $('#availableUnit').text(unit);

                // Set max for validation
                if (qty !== '') {
                    $('#quantity').attr('max', qty);
                }

                // Set unit dropdown if needed
                if (d.unit_id) {
                    $('#unit_id').val(d.unit_id);
                }

                $('#acc_warehouse').text(d.warehouse || '—');
                $('#acc_status').text(d.status || '—');
                $('#acc_expiry').text(d.expiry_date || '—');
                $('#acc_bio').text(d.biological_status || '—');
                $('#acc_sample').text(d.sample_type || '—');
                $('#acc_site').text(d.collection_site || '—');
                $('#acc_barcode').text(d.barcode || '—');

                box.removeClass('d-none');
            })
            .catch(() => box.addClass('d-none'));
    });

    if (d.unit_id) {
        $('#unit_id').val(d.unit_id); // ✅ send to backend
    }

    // Validate request qty doesn't exceed available qty
    $('#quantity').on('input', function () {
        const max = parseFloat($(this).attr('max'));
        const val = parseFloat($(this).val());

        if (!isNaN(max) && !isNaN(val) && val > max) {
            $(this).addClass('is-invalid');

            if (!$('#qtyError').length) {
                $(this).after(
                    '<div id="qtyError" class="invalid-feedback">Request quantity cannot exceed available quantity (' + max + ').</div>'
                );
            }
        } else {
            $(this).removeClass('is-invalid');
            $('#qtyError').remove();
        }
    });

    // Block form submit if qty exceeds max
    $('form').on('submit', function(e){
        const qtyInput = $('#quantity');
        const max = parseFloat(qtyInput.attr('max'));
        const val = parseFloat(qtyInput.val());
        if (!isNaN(max) && !isNaN(val) && val > max) {
            e.preventDefault();
            qtyInput.addClass('is-invalid');
            if (!$('#qtyError').length) {
                qtyInput.after('<div id="qtyError" class="invalid-feedback">Request quantity cannot exceed available quantity (' + max + ').</div>');
            }
            qtyInput[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    if (preselectedAccessionId) {

        fetch(`/lot-management/accession/${preselectedAccessionId}`)
            .then(res => res.json())
            .then(d => {

                // Step 1: Set Crop
                $('#crop_id').val(d.crop_id).trigger('change');

                // Step 2: Wait for varieties to load
                $(document).one('accessionsLoaded', function () {

                        $('#accession_id').val(d.id).trigger('change');

                    });

            });
    }

});
</script>
@endpush
