@extends('layouts.app')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height:60vh;">
    <div class="text-center">
        <div class="mb-4">
            <i class="ri-shield-cross-line text-danger" style="font-size:80px;"></i>
        </div>
        <h1 class="fw-bold text-danger" style="font-size:72px;line-height:1;">403</h1>
        <h4 class="fw-semibold mb-2">Access Denied</h4>
        <p class="text-muted mb-1">You don't have permission to access this page.</p>
        @if($exception->getMessage())
        <p class="text-muted small mb-4">
            <code>{{ $exception->getMessage() }}</code>
        </p>
        @endif
        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i> Go Back
            </a>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="ri-home-line me-1"></i> Dashboard
            </a>
        </div>
        <p class="text-muted small mt-4">
            Contact your administrator if you believe this is a mistake.
        </p>
    </div>
</div>
@endsection
