@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Warehouse Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage warehouse master data</p>
                </div>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#warehouseModal" id="addWarehouseBtn">
                    <i class="ri-add-line me-1"></i>New Warehouse
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($warehouses->count())
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>District</th>
                                    <th>City/Village</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($warehouses as $warehouse)
                                <tr data-id="{{ $warehouse->id }}">
                                    <td class="fw-500">{{ $warehouse->name }}</td>
                                    <td>
                                        @if($warehouse->code)
                                            <span class="badge bg-info">{{ $warehouse->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $warehouse->country?->country_name ?? '-' }}</td>
                                    <td>{{ $warehouse->state?->state_name ?? '-' }}</td>
                                    <td>{{ $warehouse->district?->district_name ?? '-' }}</td>
                                    <td>{{ $warehouse->city?->city_village_name ?? '-' }}</td>
                                    <td>{{ $warehouse->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $warehouse->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $warehouse->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editWarehouseBtn" data-id="{{ $warehouse->id }}"
                                                data-name="{{ $warehouse->name }}" 
                                                data-code="{{ $warehouse->code }}"
                                                data-description="{{ $warehouse->description }}"
                                                data-country="{{ $warehouse->country_id }}"
                                                data-state="{{ $warehouse->state_id }}"
                                                data-district="{{ $warehouse->district_id }}"
                                                data-city="{{ $warehouse->city_id }}">
                                            <i class="ri-edit-line"></i> 
                                        </button>
                                        <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this warehouse?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="ri-delete-bin-line"></i> 
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <p class="text-slate-500">No warehouses found. <a href="#" data-bs-toggle="modal" data-bs-target="#warehouseModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>


<script>
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.editWarehouseBtn').forEach(btn => {
        btn.addEventListener('click', function() {

            const id = this.dataset.id;

            // Basic fields
            document.getElementById('warehouseName').value = this.dataset.name;
            document.getElementById('warehouseCode').value = this.dataset.code || '';
            document.getElementById('warehouseDescription').value = this.dataset.description || '';

            // Location IDs
            let countryId = this.dataset.country;
            let stateId = this.dataset.state;
            let districtId = this.dataset.district;
            let cityId = this.dataset.city;

            // Set country
            document.getElementById('country').value = countryId;

            // 🔥 Load states
            fetch(`/get-states/${countryId}`)
                .then(res => res.json())
                .then(states => {
                    let stateSelect = document.getElementById('state');
                    stateSelect.innerHTML = '<option value="">Select State</option>';

                    states.forEach(s => {
                        stateSelect.innerHTML += `<option value="${s.id}">${s.state_name}</option>`;
                    });

                    stateSelect.value = stateId;

                    // 🔥 Load districts
                    return fetch(`/get-districts/${stateId}`);
                })
                .then(res => res.json())
                .then(districts => {
                    let districtSelect = document.getElementById('district');
                    districtSelect.innerHTML = '<option value="">Select District</option>';

                    districts.forEach(d => {
                        districtSelect.innerHTML += `<option value="${d.id}">${d.district_name}</option>`;
                    });

                    districtSelect.value = districtId;

                    // 🔥 Load cities
                    return fetch(`/get-cities/${districtId}`);
                })
                .then(res => res.json())
                .then(cities => {
                    let citySelect = document.getElementById('city');
                    citySelect.innerHTML = '<option value="">Select City</option>';

                    cities.forEach(c => {
                        citySelect.innerHTML += `<option value="${c.id}">${c.city_village_name}</option>`;
                    });

                    citySelect.value = cityId;
                });

            // Form setup
            const form = document.getElementById('warehouseForm');
            form.action = `/warehouses/${id}`;

            let method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';

            let existing = form.querySelector('input[name="_method"]');
            if (existing) existing.remove();

            form.appendChild(method);

            document.getElementById('warehouseModalLabel').textContent = 'Edit Warehouse';
            document.getElementById('submitBtn').textContent = 'Update Warehouse';

            new bootstrap.Modal(document.getElementById('warehouseModal')).show();
        });
    });

});
</script>

@endsection

@section('modals')
<!-- Warehouse Modal -->
<div class="modal fade" id="warehouseModal" tabindex="-1" role="dialog" aria-labelledby="warehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warehouseModalLabel">Add Warehouse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="warehouseForm" method="POST" action="{{ route('warehouses.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="warehouseName" class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="warehouseName" name="name" placeholder="Enter warehouse name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="warehouseCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="warehouseCode" name="code" placeholder="e.g., WH001">
                        <small class="text-danger" id="codeError"></small>
                    </div>
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country</label>
                        <select name="country_id" id="country" class="form-select">
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id', $accession->country_id ?? '') == $country->id ? 'selected' : '' }}>{{ $country->country_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">State</label>
                        <select name="state_id" id="state" class="form-select">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ old('state_id', $accession->state_id ?? '') == $state->id ? 'selected' : '' }}>{{ $state->state_name }}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">District</label>
                        <select name="district_id" id="district" class="form-select">
                            <option value="">Select District</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ old('district_id', $accession->district_id ?? '') == $district->id ? 'selected' : '' }}>{{ $district->district_name }}</option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">City/Village</label>
                        <select name="city_id" id="city" class="form-select">
                            <option value="">Select City/Village</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">
                                    {{ $city->city_village_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="warehouseDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="warehouseDescription" name="description" placeholder="Enter description" rows="3"></textarea>
                        <small class="text-danger" id="descriptionError"></small>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Warehouse</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Country → State
    document.getElementById('country').addEventListener('change', function () {
        let countryId = this.value;

        if (!countryId) {
            document.getElementById('state').innerHTML = '<option value="">Select State</option>';
            document.getElementById('district').innerHTML = '<option value="">Select District</option>';
            document.getElementById('city').innerHTML = '<option value="">Select City</option>';
            return;
        }

        fetch(`/get-states/${countryId}`)
            .then(res => res.json())
            .then(data => {
                let state = document.getElementById('state');
                state.innerHTML = '<option value="">Select State</option>';

                data.forEach(item => {
                    state.innerHTML += `<option value="${item.id}">${item.state_name}</option>`;
                });

                document.getElementById('district').innerHTML = '<option value="">Select District</option>';
                document.getElementById('city').innerHTML = '<option value="">Select City</option>';
            });
    });

    // State → District
    document.getElementById('state').addEventListener('change', function () {
        let stateId = this.value;

        if (!stateId) return;

        fetch(`/get-districts/${stateId}`)
            .then(res => res.json())
            .then(data => {
                let district = document.getElementById('district');
                district.innerHTML = '<option value="">Select District</option>';

                data.forEach(item => {
                    district.innerHTML += `<option value="${item.id}">${item.district_name}</option>`;
                });

                document.getElementById('city').innerHTML = '<option value="">Select City</option>';
            });
    });

    // District → City
    document.getElementById('district').addEventListener('change', function () {
        let districtId = this.value;

        if (!districtId) return;

        fetch(`/get-cities/${districtId}`)
            .then(res => res.json())
            .then(data => {
                let city = document.getElementById('city');
                city.innerHTML = '<option value="">Select City</option>';

                data.forEach(item => {
                    city.innerHTML += `<option value="${item.id}">${item.city_village_name}</option>`;
                });
            });
    });

});
</script>
@endsection
