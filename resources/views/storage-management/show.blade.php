@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                        Storage Details
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">{{ $storage->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('storage-management.edit', $storage->id) }}" class="btn btn-warning">
                        <i class="ri-edit-line me-1"></i>Edit Storage
                    </a>
                    <a href="{{ route('storage-management.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Back to List
                    </a>
                </div>
            </div>

            <!-- Storage Details -->
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="ri-information-line me-2"></i>Storage Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Storage ID</label>
                                        <p class="mb-0">
                                            <span class="badge bg-info">{{ $storage->storage_id }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <p class="mb-0">
                                            @if($storage->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($storage->status === 'inactive')
                                                <span class="badge bg-secondary">Inactive</span>
                                            @else
                                                <span class="badge bg-warning">Maintenance</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Storage Photo</label><br>
                                    <img src="{{ $storage->image ? asset('storage/'.$storage->image) : asset('assets/images/storage-default.jpg') }}"
         width="50"
         class="rounded">
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Name</label>
                                        <p class="mb-0">{{ $storage->name }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Type</label>
                                        <p class="mb-0">
                                            {{ $storage->storageType->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Time</label>
                                        <p class="mb-0">
                                            {{ $storage->storageTime->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Storage Condition</label>
                                        <p class="mb-0">
                                            {{ $storage->storageCondition->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Location</label>
                                        <p class="mb-0">
                                             <strong>State:</strong> {{ $storage->warehouse?->state?->state_name ?? '-' }} <br>
    <strong>District:</strong> {{ $storage->warehouse?->district?->district_name ?? '-' }} <br>
    <strong>City:</strong> {{ $storage->warehouse?->city?->city_village_name ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Capacity Information -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="ri-archive-line me-2"></i>Capacity & Usage
                                    </h6>
                                </div>

                                @if($storage->capacity)
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label fw-bold">Capacity</label>
            <p class="mb-0">
                {{ number_format($storage->capacity, 2) }} {{ $storage->unit?->name }}
            </p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label fw-bold">Current Usage</label>
            <p class="mb-0">
                {{ number_format($storage->current_usage, 2) }} {{ $storage->unit?->name }}
            </p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label fw-bold">Available</label>
            <p class="mb-0">
                {{ number_format($storage->capacity - $storage->current_usage, 2) }} {{ $storage->unit?->name }}
            </p>
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label class="form-label fw-bold">Usage Status</label>

            <div class="progress" style="height: 25px;">
                <div class="progress-bar
                    @if($storage->usage_percentage > 85) bg-danger
                    @elseif($storage->usage_percentage > 60) bg-warning
                    @else bg-success @endif"
                    style="width: {{ min($storage->usage_percentage, 100) }}%">
                    
                    {{ number_format($storage->usage_percentage, 1) }}%
                </div>
            </div>

            @if($storage->usage_percentage > 85)
                <small class="text-danger">⚠️ Storage is nearly full!</small>
            @elseif($storage->usage_percentage > 60)
                <small class="text-warning">⚠️ Storage usage is high</small>
            @else
                <small class="text-success">✅ Storage has available capacity</small>
            @endif

            <!-- 🔥 EXTRA INFO -->
            <small class="d-block text-muted mt-1">
                Used: {{ $storage->current_usage }} / {{ $storage->capacity }}
            </small>
        </div>
    </div>
    @else
    <div class="col-12">
        <div class="alert alert-info">
            <i class="ri-information-line me-2"></i>
            This storage location has unlimited capacity.
        </div>
    </div>
    @endif

                                <!-- Environment Information -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="ri-temp-hot-line me-2"></i>Environment
                                    </h6>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Temperature</label>
                                        <p class="mb-0">{{ $storage->temperature ?? 'Not specified' }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Humidity</label>
                                        <p class="mb-0">{{ $storage->humidity ?? 'Not specified' }}</p>
                                    </div>
                                </div>

                                <!-- Management Information -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="ri-user-settings-line me-2"></i>Management
                                    </h6>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Created</label>
                                        <p class="mb-0">{{ $storage->created_at->format('d M, Y H:i') }}</p>
                                    </div>
                                </div>

                                @if($storage->updated_at != $storage->created_at)
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Last Updated</label>
                                            <p class="mb-0">{{ $storage->updated_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Description -->
                                @if($storage->description)
                                    <div class="col-12">
                                        <h6 class="fw-bold text-primary mb-3">
                                            <i class="ri-file-text-line me-2"></i>Description
                                        </h6>
                                        <div class="mb-3">
                                            <p class="mb-0">{{ $storage->description }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection