@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                        Storage Management
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Manage storage locations and inventory</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('storage-management.create') }}" class="btn btn-sm btn-primary">
                        <i class="ri-add-line me-1"></i>Add New Storage
                    </a>
                </div>
            </div>

            <!-- Storage List -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="ri-store-3-line me-2"></i>Storage Locations
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if($storages->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Storage ID</th>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Time</th>
                                                <th>Location</th>
                                                <th>Capacity</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($storages as $storage)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-info">{{ $storage->storage_id }}</span>
                                                    </td>
                                                    <td>
                                                        <img src="{{ $storage->image ? asset('storage/'.$storage->image) : asset('assets/images/storage-default.jpg') }}"
         width="50"
         class="rounded">
                                                    </td>
                                                    <td>
                                                        <strong>{{ $storage->name }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $storage->storageType->name ?? 'N/A' }}
                                                    </td>

                                                    <td>
                                                        @php
                                                        $code = $storage->storageTime?->code;

                                                        $badgeClasses = [
                                                            'STS' => 'bg-success',
                                                            'MTS' => 'bg-info',
                                                            'LTS' => 'bg-danger',
                                                        ];

                                                        $class = $badgeClasses[$code] ?? 'bg-secondary';
                                                    @endphp

                                                    <span class="badge {{ $class }}">
                                                        {{ $code ?? 'N/A' }}
                                                    </span>
                                                       
                                                    </td>
                                                    <td>
                                                        {{ $storage->warehouse?->state?->state_name ?? '-' }},
                                                        {{ $storage->warehouse?->district?->district_name ?? '-' }},
                                                        {{ $storage->warehouse?->city?->city_village_name ?? '-' }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $used     = (float)($storage->current_usage ?? 0);
                                                            $capacity = (float)($storage->capacity ?? 0);
                                                            $balance  = $capacity - $used;
                                                            $pct      = $capacity > 0 ? min(($used / $capacity) * 100, 100) : 0;
                                                        @endphp
                                                        @if($capacity)
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                                    <div class="progress-bar {{ $pct > 85 ? 'bg-danger' : ($pct > 60 ? 'bg-warning' : 'bg-success') }}"
                                                                         style="width: {{ $pct }}%">
                                                                    </div>
                                                                </div>
                                                                <small class="text-muted">{{ number_format($pct, 1) }}%</small>
                                                            </div>
                                                            <small class="text-muted d-block">
                                                                Used: {{ number_format($used, 2) }} / {{ number_format($capacity, 2) }} {{ $storage->unit?->code ?? '' }}
                                                            </small>
                                                            <small class="{{ $balance < 0 ? 'text-danger' : 'text-success' }} fw-semibold">
                                                                Balance: {{ number_format($balance, 2) }} {{ $storage->unit?->code ?? '' }}
                                                            </small>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                  
                                                    <td>
                                                        @if($storage->status === 'active')
                                                            <span class="badge bg-success">Active</span>
                                                        @elseif($storage->status === 'inactive')
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @else
                                                            <span class="badge bg-warning">Maintenance</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('storage-management.show', $storage->id) }}" class="btn btn-sm btn-outline-info me-1" title="View">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                            <a href="{{ route('storage-management.edit', $storage->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                                                <i class="ri-edit-line"></i>
                                                            </a>
                                                            <form action="{{ route('storage-management.destroy', $storage->id) }}" method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Are you sure you want to delete this storage location?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="ri-store-3-line display-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">No Storage Locations Found</h5>
                                    <p class="text-muted">Get started by adding your first storage location.</p>
                                    <a href="{{ route('storage-management.create') }}" class="btn btn-primary">
                                        <i class="ri-add-line me-1"></i>Add First Storage
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection