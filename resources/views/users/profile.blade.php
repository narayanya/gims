@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- Page Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2 gap-1">
            <div>
                <h3 class="text-xl font-bold mb-0">My Profile</h3>
                <p class="text-muted mb-0" style="font-size:13px">Manage your account information and security</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">

            {{-- ── Left: Avatar + Summary ── --}}
            <div class="col-lg-3">
                <div class="card text-center">
                    <div class="card-body py-4">
                        {{-- Avatar --}}
                        <div class="position-relative d-inline-block mb-3">
                            <div class="avatar-xl mx-auto">
                                <span class="avatar-title rounded-circle bg-primary fs-1 text-white fw-bold"
                                      style="width:80px;height:80px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:2rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                    <img src="{{ 'https://vnrseeds.co.in/file-view/Employee_Image/' . session('company_id') . '/' . session('emp_code') . '.jpg' }}"
                                         alt="{{ $user->name }}"
                                         class="rounded-circle"
                                         style="width:80px;height:80px;object-fit:cover;position:absolute;top:0;left:0;">
                                </span>
                            </div>
                        </div>

                        <h5 class="mb-1 text-capitalize">{{ $user->name }}</h5>
                        <p class="text-muted mb-2" style="font-size:13px">{{ $user->email }}</p>

                        {{-- Roles --}}
                        <div class="mb-3">
                            @foreach($user->roles as $role)
                                <span class="badge bg-primary-subtle text-primary me-1">{{ $role->name }}</span>
                            @endforeach
                        </div>

                        <hr>

                        {{-- Quick Info --}}
                        <div class="text-start small">
                            @if($user->emp_code)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Emp Code</span>
                                <strong>{{ $user->emp_code }}</strong>
                            </div>
                            @endif
                            @if($user->mobile_number)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Mobile</span>
                                <strong>{{ $user->mobile_number }}</strong>
                            </div>
                            @endif
                            @if($user->reportingUser)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Reports To</span>
                                <strong>{{ $user->reportingUser->name }}</strong>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Status</span>
                                <span class="badge {{ $user->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Member Since</span>
                                <strong>{{ $user->created_at->format('M Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Right: Tabs ── --}}
            <div class="col-lg-9">

                <ul class="nav nav-tabs mb-3" id="profileTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-info">
                            <i class="ri-user-line me-1"></i> Personal Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-security">
                            <i class="ri-lock-line me-1"></i> Change Password
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-activity">
                            <i class="ri-shield-user-line me-1"></i> Roles & Permissions
                        </a>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- ── Tab 1: Personal Info ── --}}
                    <div class="tab-pane fade show active" id="tab-info">
                        <div class="card">
                            <div class="card-header bg-light">
                                <strong><i class="ri-user-line me-2"></i>Personal Information</strong>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('profile.update') }}">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input readonly type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $user->name) }}" required style="background:#f8f9fa;">
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input readonly type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $user->email) }}" required style="background:#f8f9fa;">
                                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Mobile Number</label>
                                            <input readonly type="text" name="mobile_number" class="form-control"
                                                value="{{ old('mobile_number', $user->mobile_number) }}"
                                                placeholder="e.g. +91 9876543210" style="background:#f8f9fa;">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Employee Code</label>
                                            <input readonly type="text" class="form-control" value="{{ $user->emp_code ?? '—' }}"
                                                style="background:#f8f9fa;">
                                            <small class="text-muted">Cannot be changed</small>
                                        </div>

                                        @if($user->reportingUser)
                                        <div class="col-md-6">
                                            <label class="form-label">Reporting Manager</label>
                                            <input readonly type="text" class="form-control" value="{{ $user->reportingUser->name }}"
                                                style="background:#f8f9fa;">
                                        </div>
                                        @endif

                                        <div class="col-12 text-end d-none">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-save-line me-1"></i> Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ── Tab 2: Change Password ── --}}
                    <div class="tab-pane fade" id="tab-security">
                        <div class="card">
                            <div class="card-header bg-light">
                                <strong><i class="ri-lock-line me-2"></i>Change Password</strong>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('profile.password') }}">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" name="current_password" id="currentPwd"
                                                    class="form-control @error('current_password') is-invalid @enderror"
                                                    placeholder="Enter current password" required>
                                                <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="currentPwd">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" name="password" id="newPwd"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Min 8 characters" required>
                                                <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="newPwd">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            {{-- Strength bar --}}
                                            <div class="mt-2">
                                                <div class="progress" style="height:4px;">
                                                    <div id="pwdStrengthBar" class="progress-bar" style="width:0%;transition:width .3s"></div>
                                                </div>
                                                <small id="pwdStrengthLabel" class="text-muted"></small>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" name="password_confirmation" id="confirmPwd"
                                                    class="form-control" placeholder="Repeat new password" required>
                                                <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="confirmPwd">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                            </div>
                                            <small id="pwdMatchMsg" class="mt-1 d-block"></small>
                                        </div>

                                        <div class="col-12">
                                            <div class="alert alert-info py-2 small mb-0">
                                                <i class="ri-information-line me-1"></i>
                                                Password must be at least 8 characters. Use a mix of letters, numbers and symbols for a stronger password.
                                            </div>
                                        </div>

                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="ri-lock-password-line me-1"></i> Update Password
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ── Tab 3: Roles & Permissions ── --}}
                    <div class="tab-pane fade" id="tab-activity">
                        <div class="card">
                            <div class="card-header bg-light">
                                <strong><i class="ri-shield-user-line me-2"></i>Roles & Permissions</strong>
                            </div>
                            <div class="card-body">

                                {{-- Roles --}}
                                <h6 class="text-muted border-bottom pb-2 mb-3">Assigned Roles</h6>
                                @forelse($user->roles as $role)
                                <div class="d-flex align-items-start mb-3 p-3 border rounded">
                                    <div class="avatar-xs me-3 flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-16">
                                            <i class="ri-shield-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $role->name }}</h6>
                                        <p class="text-muted mb-2 small">{{ $role->description ?? 'No description' }}</p>
                                        {{-- Permissions for this role --}}
                                        @if($role->permissions->count())
                                        <div>
                                            @foreach($role->permissions->take(12) as $perm)
                                                <span class="badge bg-light text-dark border me-1 mb-1" style="font-size:11px;">
                                                    {{ $perm->name ?? $perm->slug }}
                                                </span>
                                            @endforeach
                                            @if($role->permissions->count() > 12)
                                                <span class="badge bg-secondary ms-1">+{{ $role->permissions->count() - 12 }} more</span>
                                            @endif
                                        </div>
                                        @else
                                            <span class="text-muted small">No permissions assigned</span>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="ri-shield-off-line fs-2 d-block mb-2"></i>
                                    No roles assigned
                                </div>
                                @endforelse

                            </div>
                        </div>
                    </div>

                </div>{{-- end tab-content --}}
            </div>{{-- end col-lg-9 --}}
        </div>{{-- end row --}}

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Toggle password visibility ────────────────────────────────────────
    document.querySelectorAll('.toggle-pwd').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            const icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
            } else {
                input.type = 'password';
                icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
            }
        });
    });

    // ── Password strength meter ───────────────────────────────────────────
    const newPwd   = document.getElementById('newPwd');
    const bar      = document.getElementById('pwdStrengthBar');
    const label    = document.getElementById('pwdStrengthLabel');
    const matchMsg = document.getElementById('pwdMatchMsg');
    const confirmP = document.getElementById('confirmPwd');

    function checkStrength(pwd) {
        let score = 0;
        if (pwd.length >= 8)  score++;
        if (pwd.length >= 12) score++;
        if (/[A-Z]/.test(pwd)) score++;
        if (/[0-9]/.test(pwd)) score++;
        if (/[^A-Za-z0-9]/.test(pwd)) score++;
        return score;
    }

    if (newPwd) {
        newPwd.addEventListener('input', function () {
            const score = checkStrength(this.value);
            const levels = [
                { w: '0%',   cls: '',          text: '' },
                { w: '25%',  cls: 'bg-danger',  text: 'Weak' },
                { w: '50%',  cls: 'bg-warning', text: 'Fair' },
                { w: '75%',  cls: 'bg-info',    text: 'Good' },
                { w: '100%', cls: 'bg-success', text: 'Strong' },
            ];
            const lvl = levels[Math.min(score, 4)];
            bar.style.width = lvl.w;
            bar.className   = 'progress-bar ' + lvl.cls;
            label.textContent = lvl.text;
            label.className   = 'mt-1 small ' + (score <= 1 ? 'text-danger' : score <= 2 ? 'text-warning' : 'text-success');
            checkMatch();
        });
    }

    function checkMatch() {
        if (!confirmP.value) { matchMsg.textContent = ''; return; }
        if (newPwd.value === confirmP.value) {
            matchMsg.textContent = '✓ Passwords match';
            matchMsg.className   = 'mt-1 d-block text-success small';
        } else {
            matchMsg.textContent = '✗ Passwords do not match';
            matchMsg.className   = 'mt-1 d-block text-danger small';
        }
    }

    if (confirmP) confirmP.addEventListener('input', checkMatch);

    // ── Keep active tab after form submit (via hash) ──────────────────────
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector(`a[href="${hash}"]`);
        if (tab) bootstrap.Tab.getOrCreateInstance(tab).show();
    }

    // ── If password errors, switch to security tab ────────────────────────
    @if($errors->has('current_password') || $errors->has('password'))
    const secTab = document.querySelector('a[href="#tab-security"]');
    if (secTab) bootstrap.Tab.getOrCreateInstance(secTab).show();
    @endif

});
</script>
@endpush
