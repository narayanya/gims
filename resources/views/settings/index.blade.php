@extends('layouts.app')

@section('content')
<div class="">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Setting Management
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Manage application settings and configurations</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="ri-user-settings-line me-2"></i>Settings  
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('users') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="ri-user-line me-2"></i>Manage Users
                            </div>
                            <span class="badge bg-primary rounded-pill">User Accounts</span>
                        </a>
                        <a href="{{ route('settings.role') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="ri-shield-user-line me-2"></i>Manage Roles
                            </div>
                            <span class="badge bg-primary rounded-pill">Role-Based Access</span>
                        </a>
                        <a href="{{ route('settings.permission') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="ri-lock-line me-2"></i>Manage Permissions
                            </div>
                            <span class="badge bg-primary rounded-pill">Fine-Grained Control</span>
                        </a>
                        <a href="{{ route('logs.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="ri-lock-line me-2"></i>Log Reporting
                            </div>
                            <span class="badge bg-primary rounded-pill">Fine-Grained Control</span>
                        </a>

                        <a href="{{ route('requests.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="ri-lock-line me-2"></i>Request List
                            </div>
                            <span class="badge bg-primary rounded-pill">Fine-Grained Control</span>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    .dropdown-menu {
        min-width: 180px;
    }
</style>
@endsection
