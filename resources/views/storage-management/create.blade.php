@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                        Add New Storage Location
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Create a new storage location for inventory management</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('storage-management.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Back to List
                    </a>
                </div>
            </div>

            <!-- Storage Form -->
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="ri-add-line me-2"></i>Storage Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('storage-management.store') }}" method="POST" id="storageForm" enctype="multipart/form-data">
                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Please fix the following errors:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <!-- Basic Information Section -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <h6 class="fw-bold text-primary mb-3">
                                            <i class="ri-information-line me-2"></i>Basic Information
                                        </h6>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Warehouse Name <span class="text-danger">*</span></label>

                                        <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                            <option value="">Select Warehouse Name</option>

                                            @foreach($storageWarehouse as $warehouse)
                                                <option value="{{ $warehouse->id }}"
                                                    data-state="{{ $warehouse->state?->state_name }}"
                                                    data-district="{{ $warehouse->district?->district_name }}"
                                                    data-city="{{ $warehouse->city?->city_village_name }}"
                                                    {{ old('warehouse_id', $storage->warehouse_id ?? '') == $warehouse->id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span id="warehouseLocationText">
                                        </span>
                                        @error('storage_time_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Storage Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    

                                    <div class="col-md-3">
                                        <label class="form-label">Storage Time <span class="text-danger">*</span></label>

                                        <select name="storage_time_id" class="form-select @error('storage_time_id') is-invalid @enderror" required>
                                            <option value="">Select Storage Time</option>

                                            @foreach($storageTime as $time)
                                                <option value="{{ $time->id }}"
                                                    {{ old('storage_time_id') == $time->id ? 'selected' : '' }}>
                                                    {{ $time->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('storage_time_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Storage Condition <span class="text-danger">*</span></label>

                                        <select name="storage_condition_id" class="form-select @error('storage_condition_id') is-invalid @enderror" required>
                                            <option value="">Select Condition</option>

                                            @foreach($storageCondition as $condition)
                                                <option value="{{ $condition->id }}"
                                                    {{ old('storage_condition_id', $storage->storage_condition_id ?? '') == $condition->id ? 'selected' : '' }}>
                                                    {{ $condition->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('storage_condition_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="type" class="form-label">
                                            Storage Type <span class="text-danger">*</span>
                                        </label>

                                        <select class="form-select @error('type') is-invalid @enderror"
                                                id="type" name="type" required>
                                            <option value="">Select Type</option>

                                            @foreach($storageTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('type', $storage->storage_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                   
                                    <div class=" col-md-3 mb-3">
                                        <label class="form-label">Storage Image <span class="text-danger">*</span></label>
                                        <input type="file" name="image" class="form-control" required>
                                    </div>
                                </div>

                                <!-- Capacity Section -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <h6 class="fw-bold text-primary mb-3">
                                            <i class="ri-archive-line me-2"></i>Capacity & Environment
                                        </h6>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="capacity" class="form-label">Capacity</label>
                                        <input type="number" step="0.01" class="form-control @error('capacity') is-invalid @enderror"
                                               id="capacity" name="capacity" value="{{ old('capacity') }}"
                                               placeholder="0.00">
                                        @error('capacity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                                        <select class="form-select @error('unit') is-invalid @enderror"
                                            id="unit" name="unit" required>

                                        <option value="">Select Unit</option>

                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ old('unit', $storage->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }} ({{ $unit->code }})
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                       
                                    </div>

                                    <div class="col-md-4">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror"
                                                id="status" name="status" required style="width: 100%;height:auto;position: relative;top: 0;margin: 0;left: 0;">
                                            <option value="" selected>Select Status</option>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="temperature" class="form-label">Temperature</label>
                                        <input type="text" class="form-control @error('temperature') is-invalid @enderror"
                                               id="temperature" name="temperature" value="{{ old('temperature') }}"
                                               placeholder="e.g., -20°C, Room Temperature">
                                        @error('temperature')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="humidity" class="form-label">Humidity</label>
                                        <input type="text" class="form-control @error('humidity') is-invalid @enderror"
                                               id="humidity" name="humidity" value="{{ old('humidity') }}"
                                               placeholder="e.g., 40-60%">
                                        @error('humidity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Description Section -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <h6 class="fw-bold text-primary mb-3">
                                            <i class="ri-file-text-line me-2"></i>Additional Information
                                        </h6>
                                    </div>

                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  id="description" name="description" rows="3"
                                                  placeholder="Additional notes about this storage location...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('storage-management.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-save-line me-1"></i>Create Storage
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation and enhancements
document.getElementById('storageForm').addEventListener('submit', function(e) {
    const capacity = document.getElementById('capacity').value;
    const unit = document.getElementById('unit').value;

    if (capacity && !unit) {
        e.preventDefault();
        alert('Please select a unit when specifying capacity.');
        document.getElementById('unit').focus();
    }
});

document.addEventListener('DOMContentLoaded', function () {

    const warehouseSelect = document.querySelector('[name="warehouse_id"]');
    const locationText = document.getElementById('warehouseLocationText');

    function updateLocation() {
        let selected = warehouseSelect.options[warehouseSelect.selectedIndex];

        let state = selected.getAttribute('data-state') || '-';
        let district = selected.getAttribute('data-district') || '-';
        let city = selected.getAttribute('data-city') || '-';

        locationText.innerHTML = `<b>State:</b> ${state}, <b>District:</b> ${district}, <b>City/Village:</b> ${city}`;
    }

    // On change
    warehouseSelect.addEventListener('change', updateLocation);

    // On page load (edit case)
    updateLocation();

});
</script>
@endsection